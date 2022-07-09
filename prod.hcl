locals {
  tag = "v2.0.0-alpha.25"
  postgres_version = "14"
  server_name = "https://localhost"
}

job "sicope-model-dev" {
  datacenters = ["dc1"]

  group "postgres" {
    network {
      port "postgres" {
        to = "5432"
      }
    }
    volume "postgres" {
      type      = "host"
      read_only = false
      source    = "postgres"
    }
    task "postgres" {
      driver = "docker"
      config {
        image = "postgres:${local.postgres_version}-alpine"
        ports = ["postgres"]
      }
      volume_mount {
        volume      = "postgres"
        destination = "/var/lib/postgresql/data"
        read_only   = false
      }
      template {
        data = <<EOH
        {{ with secret "secret/data/sicope-model" }}
        POSTGRES_USER="{{.Data.postgres_user}}"
        POSTGRES_PASSWORD="{{.Data.postgres_password}}"
        POSTGRES_DB="{{.Data.postgres_db}}"
        {{ end }}
        EOH

        destination = "${NOMAD_SECRETS_DIR}/config.env"
        env         = true
      }
      service {
        name     = "postgres"
        provider = "nomad"
        port     = "postgres"
      }
    }
  }

  group "caddy" {
    network {
      port "http" {
        to = "80"
      }
      port "https" {
        static = "443"
        to = "443"
      }
    }

    task "caddy" {
      driver = "docker"
      config {
        network_mode = "host"
        image = "tienvx/sicope-model-caddy:${local.tag}"
        ports = ["http", "https"]
      }
      service {
        name     = "caddy-http"
        provider = "nomad"
        port     = "http"
      }
      service {
        name     = "caddy-https"
        provider = "nomad"
        port     = "https"
      }
      template {
        data = <<EOH
        {{ range nomadService "admin" }}
        PHP_FPM_ADDRESS="{{ .Address }}:{{ .Port }}"
        {{ end }}
        EOH

        destination = "${NOMAD_SECRETS_DIR}/config.env"
        env         = true
      }
      env {
        SERVER_NAME = "${local.server_name}"
      }
    }
  }

  group "admin" {
    network {
      port "fpm" {
        to = "9000"
      }
    }
    task "admin" {
      driver = "docker"
      config {
        image = "tienvx/sicope-model-admin:${local.tag}"
        ports = ["fpm"]
      }
      template {
        data = <<EOH
        APP_ENV="prod"
        DB_EXTENSION="Postgresql"
        {{ with secret "secret/data/sicope-model" }}
        DATABASE_URL="postgresql://{{.Data.data.postgres_user}}:{{.Data.data.postgres_password}}@{{ range nomadService "postgres" }}{{ .Address }}:{{ .Port }}{{ end }}/{{.Data.data.postgres_db}}?serverVersion=${local.postgres_version}&charset=UTF-8"
        STATUS_URI="{{.Data.data.status_uri}}"
        WEBDRIVER_URI="{{.Data.data.webdriver_uri}}"
        MAILER_DSN="{{.Data.data.mailer_dsn}}"
        APP_SECRET="{{.Data.data.app_secret}}"
        {{ end }}
        {{ range nomadService "redis" }}
        REDIS_DSN="redis://{{ .Address }}:{{ .Port }}"
        MESSENGER_TRANSPORT_DSN="redis://{{ .Address }}:{{ .Port }}/messages"
        {{ end }}
        EOH

        destination = "${NOMAD_SECRETS_DIR}/config.env"
        env         = true
      }
      service {
        name     = "admin"
        provider = "nomad"
        port     = "fpm"
      }
    }
  }

  group "worker" {
    task "wait-for-db" {
      lifecycle {
        hook = "prestart"
        sidecar = false
      }

      #driver = "exec"
      driver = "raw_exec"
      config {
        command = "sh"
        args = ["-c", "while ! nc -z ${POSTGRES_HOST} ${POSTGRES_PORT}; do sleep 1; done"]
      }
      template {
        data = <<EOH
        {{ range nomadService "postgres" }}
        POSTGRES_HOST={{ .Address }}
        POSTGRES_PORT={{ .Port }}
        {{ end }}
        EOH

        destination = "${NOMAD_SECRETS_DIR}/config.env"
        env         = true
      }
    }

    task "wait-for-redis" {
      lifecycle {
        hook = "prestart"
        sidecar = false
      }

      #driver = "exec"
      driver = "raw_exec"
      config {
        command = "sh"
        args = ["-c", "while ! nc -z ${REDIS_HOST} ${REDIS_PORT}; do sleep 1; done"]
      }
      template {
        data = <<EOH
        {{ range nomadService "redis" }}
        REDIS_HOST={{ .Address }}
        REDIS_PORT={{ .Port }}
        {{ end }}
        EOH

        destination = "${NOMAD_SECRETS_DIR}/config.env"
        env         = true
      }
    }

    task "worker" {
      driver = "docker"
      config {
        network_mode = "host"
        image = "tienvx/sicope-model-worker:${local.tag}"
      }
      template {
        data = <<EOH
        APP_ENV="prod"
        DB_EXTENSION="Postgresql"
        {{ with secret "secret/data/sicope-model" }}
        DATABASE_URL="postgresql://{{.Data.data.postgres_user}}:{{.Data.data.postgres_password}}@{{ range nomadService "postgres" }}{{ .Address }}:{{ .Port }}{{ end }}/{{.Data.data.postgres_db}}?serverVersion=${local.postgres_version}&charset=UTF-8"
        WEBDRIVER_URI="{{.Data.data.webdriver_uri}}"
        MAILER_DSN="{{.Data.data.mailer_dsn}}"
        APP_SECRET="{{.Data.data.app_secret}}"
        {{ end }}
        {{ range nomadService "redis" }}
        REDIS_DSN="redis://{{ .Address }}:{{ .Port }}"
        MESSENGER_TRANSPORT_DSN="redis://{{ .Address }}:{{ .Port }}/messages"
        {{ end }}
        EOH

        destination = "${NOMAD_SECRETS_DIR}/config.env"
        env         = true
      }
    }
  }

  group "redis" {
    network {
      port "redis" {
        to = "6379"
      }
    }

    task "redis" {
      driver = "docker"
      config {
        image = "redis"
        ports = ["redis"]
      }
      service {
        name     = "redis"
        provider = "nomad"
        port     = "redis"
      }
    }
  }
}
