# gossamerCMS2.0

Architecture borrows best practices from Java Enterprise approach including design patterns for security, OOP structure, configuration-based approach, filters, event listeners

## Database / datastore agnostic
- Not hardwired to a database; everything is done via REST calls and/or Client API calls
- Can work with multiple databases via configuration
- Credentials based
- If changing from database types, can extend existing classes in configuration
- Define the datasource to the URI endpoint
- ie. /GET could be different than 
- Customize calls to different datasources in config vs in the code

## Security
- Configuration defines authorization to URI level, role level
- Avoids code-level security issues
- Designed to manage through filters & event listeners, similar to a Java Enterprise design pattern

Behaviour of framework is configuration based not code based
- Handling of calls, returns is in configuration

## Examples:
- If we want to change data source… 

## Production, Staging & Testing
- Environment based configuration
- Staging environment configuration with ability to provide mock-data responses
- Configure sample data for endpoint testing and prototyping that avoids database connection if desired

## PHP based advantages
- Proven language with strong documentation, community support
- Modern version is faster with compiled 
- Type-scripted language enforces strong datatypes
- Simple, efficient language without sacrificing security, speed or control
- Broad support with online, community and professional resources available 
- Cost effective with no licensing or proprietary software required




complete rewrite from the ground up of the gossamer CMS framework

All separate repositories (json/html and database) are now 1 unified repository, specified in /app/config/config/yml.
Future: add an API setting

The EventListener has been lightened up, and the system only uses the EventListener for raised events - natural flow is now handled through Filters.

