# Contributing

Contributions are welcome and will be fully credited.

## Pull Requests

- **[PSR-12 Coding Standard](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-12-extended-coding-style-guide.md)** - The easiest way to apply the conventions is to install [PHP Code Sniffer](https://github.com/squizlabs/PHP_CodeSniffer).

- **Add tests!** - Your patch won't be accepted if it doesn't have tests.

- **Document any change in behaviour** - Make sure the `README.md` and any other relevant documentation are kept up-to-date.

- **Consider our release cycle** - We try to follow [SemVer v2.0.0](https://semver.org/). Randomly breaking public APIs is not an option.

- **Create feature branches** - Don't ask us to pull from your master branch.

- **One pull request per feature** - If you want to do more than one thing, send multiple pull requests.

- **Send coherent history** - Make sure each individual commit in your pull request is meaningful. If you had to make multiple intermediate commits while developing, please [squash them](https://www.git-scm.com/book/en/v2/Git-Tools-Rewriting-History#Changing-Multiple-Commit-Messages) before submitting.

## Running Tests

```bash
composer test
```

## Creating New Components

When creating new components, please follow these guidelines:

1. **Naming Convention**
   - Use clear, descriptive names
   - Follow existing patterns for similar components
   - Add PHPDoc blocks with proper descriptions

2. **Component Structure**
   - Implement all required interfaces
   - Follow the builder pattern consistently
   - Add proper validation rules
   - Include default values where appropriate

3. **Documentation**
   - Add PHPDoc blocks to all public methods
   - Include usage examples in the component's documentation
   - Update the README.md with new component information

4. **Testing**
   - Write unit tests for all new components
   - Include edge cases in your tests
   - Test validation rules
   - Test builder methods

## Security

If you discover any security related issues, please email skillcraft.opensource@pm.me instead of using the issue tracker.

## Credits

Thank you to all the people who have already contributed to UI Component!
