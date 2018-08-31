# gossamerCMS2.0

Architecture borrows best practices from Java Enterprise approach including design patterns for security, OOP structure, configuration-based approach, filters, event listeners

## Database / datastore agnostic
The framework does not connect directly to a database like most traditional frameworks; everything is done via REST calls and/or Client API calls. This permits the framework to connect to multiple datasources via a credentials file. Datasource connections can be configured individually at the granulairty of each request URI endpoint by use of a configuration file (routing.yml) located in each component directory. This means it can be configured to connect to 1 datasource on a URI's [GET] request and an alternate datasource on the same URI's [POST] request. If a change to the datasource is required there is no need to change the code, simply change the datasource key in the configuration file for that requested URI endpoint.
- REST based datasource connectivity
- Can work with multiple databases via configuration
- Credentials based
- If changing from database types, can extend existing classes in configuration
- Define the datasource to the URI endpoint
- ie. /GET could be different than 
- Customize calls to different datasources in config vs in the code

## Security
Security can be defined for each request URI endpoint. This permits the configuration for authentication AND authorization handlers for each endpoint without the need for writing the code into a controller. Authentication can be defined in the app level firewall.yml file with individual authorization handlers defined in each component's security.yml file.
- Configuration defines authorization to URI level, role level
- Avoids code-level security issues
- Designed to manage through filters & event listeners, similar to a Java Enterprise design pattern

Behaviour of framework is configuration based not code based
- Handling of calls, returns is in configuration

## Examples:
- If we want to change data source:
```
members_save:
    pattern: '/members/{id}'
    defaults:
        component: components\members\MembersComponent
        controller: components\members\controllers\MembersController
        model: components\members\models\MemberModel
        method: save
        view: Gossamer\Core\Views\JSONView
        viewKey: members_save
        datasource: datasource1
    methods: [POST]
```

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

