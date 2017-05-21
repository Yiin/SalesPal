
### Some rules
To make the contribution process nice and easy for anyone, please follow some rules:
 * Only one feature/bugfix per issue. If you want to submit more, create multiple issues.
 * Only one feature/bugfix per PR(pull request). Split more changes into multiple PRs.

#### Coding Style
Try to follow the [PSR-2 guidlines](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md)

_Example styling:_
```php
/**
 * Gets a preview of the email
 *
 * @param TemplateService $templateService
 *
 * @return \Illuminate\Http\Response
 */
public function previewEmail(TemplateService $templateService)
{
    //
}
```
