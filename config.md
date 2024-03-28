# Config

## Container Configuration

This configuration is used to associate an HTTP request with a service (Docker container).
When no match with the container, the request will match the {service_name}.

### Development

| container      | config | value                       |
|----------------|--------|-----------------------------|
|                |        |                             |
| web            | host   | dev.camping-tools.localhost |
|                | path   | /                           |
|                |        |                             |
| {service_name} | host   | dev.camping-tools.localhost |
|                | path   | /{service_name}/conf_api    |

### Production

| container      | config | value                    |
|----------------|--------|--------------------------|
|                |        |                          |
| web            | host   | camping-tools.com        |
|                |        | camping-tools.nl         |
|                |        | camping-tools.fr         |
|                |        | camping-tools.de         |
|                | path   | /                        |
|                |        |                          |
| {service_name} | host   | camping-tools.com        |
|                | path   | /{service_name}/conf_api |
