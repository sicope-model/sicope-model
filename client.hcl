vault {
  enabled = true
  address = "http://127.0.0.1:8200"
  token = "<vault token>"
}

client {
  host_volume "postgres" {
    path      = "/path/to/var/postgres-data"
    read_only = false
  }
}
