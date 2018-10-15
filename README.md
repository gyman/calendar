
1. Run migration

    ```bash
    ./bin/console doctrine:migrations:migrate
    ```

2. Create schema

   ```bash
    ./bin/console doctrine:schema:update -f
    ``` 
    
3. Create event stream

    ```bash
    ./bin/console event-store:event-stream:create
    ```
    
4. Run projections to have read models populated

    ```bash
    ./bin/console event-store:projection:run calendar
    ```
    
    and to have snapshots:
    
    ```bash
    ./bin/console event-store:projection:run calendar_snapshot
    ```