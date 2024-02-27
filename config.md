# Container Configuration

This configuration is used to associate an HTTP request with a service (Docker container).

## Development

| container/config      | value                       |
|-----------------------|-----------------------------|
|                       |                             |
| web/host              | dev.camping-tools.localhost |
| web/path              | /                           |
|                       |                             |
| {container_name}/host | dev.camping-tools.localhost |
| {container_name}/path | /{service_name}/api         |

## Production

| container/config    | value               |
|---------------------|---------------------|
|                     |                     |
| web/host            | camping-tools.com   |
| web/host            | camping-tools.nl    |
| web/host            | camping-tools.fr    |
| web/host            | camping-tools.de    |
| web/path            | /                   |
|                     |                     |
| {service_name}/host | camping-tools.com   |
| {service_name}/path | /{service_name}/api |



