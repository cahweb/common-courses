# Common - Courses

A WordPress plugin that provides a shortcode to display a table of courses for a given department.

## External dependecies

- Athena Bootstrap (CSS/JS)
- Athena Bootstrap jQquery (for modals and tooltips)

## Installation

1. Git pull or download/extract to the WordPress plugins directory.
2. Create a `db_config.php` file in the `includes` directory.
3. Edit and add the database credentials to the `includes/db_config.php` like so:

```php
<?

return array(
    "server" => "your server address here",
    "user" => "your user here",
    "pass" => "your password here",
    "db" => "your database here",
);

?>
```

## Usage

### Default
Create a WordPress page and add this shortcode.

```
[courses]
```

This will display a table of courses for a term. The range of terms by default are [`current term - 1`, `current term`, `current term + 1`].

### Alternative term ranges (optional)

If a specific range of terms is required, add the `start` and `end` parameters. They are case-insensitive.

```
[courses start="summer 2020" end="spring 2021"]
```

Keep in mind, both options must be filled if used. One or the other cannot be left blank.

If a value is misspelled or unfound, an error message will be displayed with a list of valid terms.

### Alternative department (optional)

The default department shown is the one declared in the WordPress theme with the constant variable `DEPT`.

Overriding is possible with the `dio` parameter and the wanted department ID.

```
[courses dio="5"]
```