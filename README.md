# gossamerCMS 2.0

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

## Configurable return types
Return types are specified in the configuration of a requested URI endpoint - not in the code. This means response types (eg: JSON, HTML, etc..) can be changed without the need to touch the written code. It also means that the same controller methods can be called for different endpoints AND send out different response types depending on the device (eg: JSON for mobile, HTML for desktop) while the method itself is completely agnostic of its response type.

## Security
Security can be defined for each request URI endpoint. This permits the configuration for authentication AND authorization handlers for each endpoint without the need for writing the code into a controller. Authentication can be defined in the app level firewall.yml file with individual authorization handlers defined in each component's security.yml file.
- Configuration defines authorization to URI level, role level
- Avoids code-level security issues
- Designed to manage through filters & event listeners, similar to a Java Enterprise design pattern

Behaviour of framework is configuration based not code based
- Handling of calls, returns is in configuration

## Examples:
- If we want to change data source for this request from **datasource1** to **datasource2** which will make a call to a different remote endoint without the need to alter the written code:
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
we can change to a separate datasource for this request only:
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
        datasource: datasource2
    methods: [POST]
```

## Production, Staging & Testing with build in mocking
Each request URI endpoint has the ability to be mocked during testing and development even before any actual methods have been created by a developer. Simply add **mocked: true** to a node configuration:
```
members_get:
    pattern: '/members/{id}'
    defaults:
        component: components\members\MembersComponent
        controller: components\members\controllers\MembersController
        model: components\members\models\MemberModel
        method: get
        view: Gossamer\Core\Views\JSONView
        viewKey: members_get
        datasource: datasource1
    methods: [GET]
    mocked: true
```
This will ignore the existing controller and load a preconfigured json response to send out - no need to comment/alter code during testing. Mocked responses are configured in a mocks.yml file in the component config:
```
members_get:
    fullName: David Meikle
    firstname: David
    lastname: Meikle
    email: david@quantumunit.com
    location: Toronto, ON
    areasOfImpact: unknown
    impactType: unknown
    groups: unknown
    organizations: unknown
    charities: unknown
```
which will generate the following JSON response:
{
  "organizations": "unknown", 
  "firstname": "David", 
  "lastname": "Meikle", 
  "areasOfImpact": "unknown", 
  "charities": "unknown", 
  "location": "Toronto, ON", 
  "groups": "unknown", 
  "fullName": "David Meikle", 
  "impactType": "unknown", 
  "email": "david@quantumunit.com"
}

- Environment based configuration
- Staging environment configuration with ability to provide mock-data responses
- Configure sample data for endpoint testing and prototyping that avoids database connection if desired

## PHP based advantages
- Proven language with strong documentation, community support
- PHP7 version is faster with caching of compiled scripts 
- Type-scripted language enforces strong datatypes
- Return type declarations
- Simple, efficient language without sacrificing security, speed or control
- Broad support with online, community and professional resources available 
- Cost effective with no licensing or proprietary software required



## Version 2.0 Release Notes
complete rewrite from the ground up of the gossamer CMS framework

All separate repositories (json/html and database) are now 1 unified repository, specified in /app/config/config/yml.
Future: add an API setting

The EventListener has been lightened up, and the system only uses the EventListener for raised events - natural flow is now handled through Filters.

