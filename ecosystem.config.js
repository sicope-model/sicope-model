module.exports = {
    apps: [
        {
            name: 'queue',
            interpreter: 'php',
            script: 'bin/console',
            args: 'messenger:consume async --memory-limit=256M --time-limit=3600',
            instances: 2,
            autorestart: true,
            watch: false,
            max_memory_restart: '1G',
        },
        {
            name: 'cron',
            interpreter: 'php',
            script: 'bin/console',
            args: 'schedule:run',
            exec_mode: "fork",
            cron_restart: "* * * * *",
            instances: 1,
            autorestart: false,
            watch: false,
        }
    ]
};
