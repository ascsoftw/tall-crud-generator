# tall-crud-generator
Laravel Package to generate CRUD Files using TALL Stack. This is not an admin panel generator, it is a package that generates Livewire Components that supports CRUD Features without you having to write any single line of code.

<p align="center">
  <img src="https://media.giphy.com/media/e4sld9tmOsDMMbBztN/giphy.gif">
</p>

## Requirements

Make sure that [Livewire](https://laravel-livewire.com/) is installed properly on your project.

Make sure that [TailwindCSS](https://tailwindcss.com/) is installed properly on your project.

Make sure that [AlpineJS](https://github.com/alpinejs/alpine/) is installed properly on your project.

## Installation

You can install the Package using Composer

```bash
composer require ascsoftw/tall-crud-generator
```

## Usage

After you have installed the package, you can display the Config Page by including the Livewire Component in any View.

```php
@livewire('tall-crud-generator')
```

This way you can display the Config Page using your Application Layout and can also control the Users who have access to the Page.


## Configurations

If you want to override the configurations, you can publish the View and Blade Components used using the below command
```bash
php artisan vendor:publish --provider="Ascsoftw\TallCrudGenerator\TallCrudGeneratorServiceProvider" --tag=views
```

This will publish the Views in `resources/views/vendor/tall-crud-generator` directory which you can then customize. Most of the Components being used are taken from Breeze and Jetstream Packages and so should be familiar.

## Wiki

Please check [Wiki](https://github.com/ascsoftw/tall-crud-generator/wiki) for more Details and Video Tutorials.

## FAQ
**Question:** There are many CRUD Packages available. Why use this Package?

**Answer:** Unlike other Packages, this Package works by generateing the actual Livewire Component. You are free to use it anywhere in your Project. It is also easier to customize them since you are editing the Livewire Files rather than figuring out the Configurable Options of the Package.

##
**Q:** Package doesn't support a Feaure that I need. What should I do?

**A:** The Package is at the initial stages and it only supports small number of Features. We are working on adding New Features. However, since the package generates the Actual Files, you can always Edit those Files and customize them according to your needs.

## Features

Following are some of the Features that are supported:
- Pagination
- Sorting
- Searching
- Add Form in Modal
- Edit Form in Modal
- Delete with Modal Confirmation
- Validations
- Configure the Order of Columns in Listing
- Configure Field Type for Form
- Configure the Order for Form Fields
- Flash Messages
- Configuring No. of Records Per Page
- Display Dropdown to change no. of Records Per Page
- Relations: BelongsTo & BelongsToMany in Listing & Form
- Display Related Fields in the Listing using Eager Loading
- Display Count of Related Fields in the Listing using Eager Loading Count
- Having the Ability for User to Show / Hide Columns on Listing
- Allow User to Define Filters
- Bulk Action
- Date Filters

## Testing

```bash
composer test
```

## Troubleshooting
Your Output don't get styles while using TailwindCss? Please publish your view. Therefore Laravel Mix compiler will find package related views and will purge CSS accordingly.

## Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

## Credits

- [AscSoftwares](http://www.ascsoftwares.com)

## License
[MIT](https://choosealicense.com/licenses/mit/)
