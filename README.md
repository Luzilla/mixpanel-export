# mixpanel export

Export data from mixpanel.

## Setup

 1. `git clone`
 2. `composer install`

## Usage

```
$ ./export.php export -k <api-key> -s <api-secret> <method>
```

This creates data dumps in `var/` (usually prefixed by date, followed by `<method>`).

The `<method>` could be `engage`, for more info:
https://mixpanel.com/docs/api-documentation/data-export-api
