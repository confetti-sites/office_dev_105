# Container Configuration

This configuration is used to match a HTTP request to a service (Docker container).
When a container exists without a matching configuration, the configurations with prefix `/default` key is used.

## Development

| key               | value                                 | description                     |
|-------------------|---------------------------------------|---------------------------------|
| /default          | *                                     | Match all other container names |
| /default/hosts~1  | office_dev_105.confetti-cms.localhost | Match container when this host  |
| /default/path~1   | /{service_name}/conf_api              | Match container when this path  |
| /view-php         | confetti-cms/view-php                 | Name of the container           |
| /view-php/hosts~1 | office_dev_105.confetti-cms.localhost | Match container when this host  |
| /view-php/path~1  | /                                     | Match container when this path  |

## Production

| key               | value                    | description                     |
|-------------------|--------------------------|---------------------------------|
| /default          | *                        | Match all other container names |
| /default/hosts~1  | confetti-cms.com         | Match container when this host  |
| /default/path~1   | /{service_name}/conf_api | Match container when this path  |
| /view-php         | confetti-cms/view-php    | Name of the container           |
| /view-php/hosts~1 | confetti-cms.com         | Match container when this host  |
| /view-php/path~1  | /                        | Match container when this path  |






