

## Development
[/containers/environment~development]

| key               | value                                 | description                     |
|-------------------|---------------------------------------|---------------------------------|
| /name             | sandbox                               | Name of the environment         |
| /default          | *                                     | Match all other container names |
| /default/hosts~1  | office_dev_105.confetti-cms.localhost | Match container when this host  |
| /default/path~1   | /{service}/conf_api                   | Match container when this path  |
| /view-php         | confetti-cms/view-php                 | Name of the container           |
| /view-php/hosts~1 | office_dev_105.confetti-cms.localhost | Match container when this host  |
| /view-php/path~1  | /                                     | Match container when this path  |

# Production
[/containers/environment~production]

| key               | value                 | description                     |
|-------------------|-----------------------|---------------------------------|
| /name             | production            | Name of the environment         |
| /default          | *                     | Match all other container names |
| /default/hosts~1  | confetti-cms.com      | Match container when this host  |
| /default/path~1   | /{service}/conf_api   | Match container when this path  |
| /view-php         | confetti-cms/view-php | Name of the container           |
| /view-php/hosts~1 | confetti-cms.com      | Match container when this host  |
| /view-php/path~1  | /                     | Match container when this path  |






