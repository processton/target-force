# Targetforce Core

## 2.0.3 - 2021-07-05
- Self-host CSS and JS assets ([#144](https://github.com/mettle/targetforce-core/pull/144))
- Handle merge content and subject when subscriber first name or last name are null ([#143](https://github.com/mettle/targetforce-core/pull/143))
- Add german translation ([#140](https://github.com/mettle/targetforce-core/pull/140))
- PHP 8: Bug fix for str_ireplace - empty values ([#133](https://github.com/mettle/targetforce-core/pull/133))
- Update GitHub workflow for PHP CS Fixer v3 ([#130](https://github.com/mettle/targetforce-core/pull/130))
- Update dependencies

## 2.0.2 - 2021-04-30
- Add matrix strategy to cover all supported php versions ([#127](https://github.com/mettle/targetforce-core/pull/127))
- Tag Pagination ([#126](https://github.com/mettle/targetforce-core/pull/126))
- Keep query string for subscribers pagination ([#122](https://github.com/mettle/targetforce-core/pull/122))
- Support PHP 8 ([#120](https://github.com/mettle/targetforce-core/pull/120))
- Update dependencies

## 2.0.1 - 2021-03-08
- Message subject tags ([#114](https://github.com/mettle/targetforce-core/pull/114))
- Add SMTP driver ([#112](https://github.com/mettle/targetforce-core/pull/112))
- Split posts index ([#111](https://github.com/mettle/targetforce-core/pull/111))
- Delete failed messages on subscriber deletion ([#110](https://github.com/mettle/targetforce-core/pull/110))
- Fix rename segments to tags migration ([#115](https://github.com/mettle/targetforce-core/pull/115))
- Fix table prefixing in raw query ([#107](https://github.com/mettle/targetforce-core/pull/107))

## 2.0.0 - 2021-02-08
- Allow Core to be installed as a package in an existing project ([#85](https://github.com/mettle/targetforce-core/pull/85))
- Bump Laravel to v8 ([#103](https://github.com/mettle/targetforce-core/pull/103))

## 1.0.10 - 2020-10-15
- Resolve invalid message issue ([#87](https://github.com/mettle/targetforce-core/pull/87))

## 1.0.9 - 2020-10-09
- Set Subscriber unsubscribed_at via API ([#83](https://github.com/mettle/targetforce-core/pull/83))
- Adjust quota service ([#82](https://github.com/mettle/targetforce-core/pull/82))
- Ensure template names are unique ([#81](https://github.com/mettle/targetforce-core/pull/81))
- Gitignore php_cs.cache ([#80](https://github.com/mettle/targetforce-core/pull/80))
- Validate imported subscribers ([#79](https://github.com/mettle/targetforce-core/pull/79))
- Added ability to delete draft messages ([#78](https://github.com/mettle/targetforce-core/pull/78))
- Fix post count formatting ([#77](https://github.com/mettle/targetforce-core/pull/77))
- Make password hidden in setup command ([#76](https://github.com/mettle/targetforce-core/pull/76))
- Filter subscribers by segment ([#75](https://github.com/mettle/targetforce-core/pull/75))
- Move ping route to controller ([#73](https://github.com/mettle/targetforce-core/pull/73))
- Default posts to send to all ([#71](https://github.com/mettle/targetforce-core/pull/71))
- Validate segments when creating a post ([#70](https://github.com/mettle/targetforce-core/pull/70))
- Add GUI setup process ([#68](https://github.com/mettle/targetforce-core/pull/68))
- Clean up template deletion ([#67](https://github.com/mettle/targetforce-core/pull/67))
- Add ability to cancel posts ([#66](https://github.com/mettle/targetforce-core/pull/66))
- Add backslash to language files ([#65](https://github.com/mettle/targetforce-core/pull/65))

## 1.0.7 - 2020-08-14

- Add ability to delete a subscriber ([#60](https://github.com/mettle/targetforce-core/pull/60))
- Fix behaviour that removed subscribers on segment update ([#61](https://github.com/mettle/targetforce-core/pull/61))
- Add ability to test an e-mail service ([#62](https://github.com/mettle/targetforce-core/pull/62))

## 1.0.6 - 2020-08-07

- Add GH action for php-cs ([#35](https://github.com/mettle/targetforce-core/pull/35))
- Add Post Endpoints to API ([#40](https://github.com/mettle/targetforce-core/pull/40))
- Uniform mail adapters send method ([#51](https://github.com/mettle/targetforce-core/pull/51))
- Add Sqlite support and defaults the test suite to use the sqlite database ([#52](https://github.com/mettle/targetforce-core/pull/52))
- Fix count unique opens per period in Postgres ([#53](https://github.com/mettle/targetforce-core/pull/53))
- Bump elliptic from 6.5.2 to 6.5.3 ([#55](https://github.com/mettle/targetforce-core/pull/55))
- add from name to outgoing emails ([#56](https://github.com/mettle/targetforce-core/pull/56))
- add mailjet support ([#57](https://github.com/mettle/targetforce-core/pull/57))

## 1.0.5 - 2020-08-03

- bump lodash version ([#44](https://github.com/mettle/targetforce-core/pull/44))
- fix primary key when chunking on segments ([#54](https://github.com/mettle/targetforce-core/pull/54))

## 1.0.4 - 2020-07-21

- fix SES adapter ([#42](https://github.com/mettle/targetforce-core/pull/42))

## 1.0.3 - 2020-07-11

- added CHANGELOG.md ([#39](https://github.com/mettle/targetforce-core/pull/39))
- added API for templates ([#38](https://github.com/mettle/targetforce-core/pull/38))
- add validation for deleting templates that are in use ([#37](https://github.com/mettle/targetforce-core/pull/37))
- add Template API endpoints ([#38](https://github.com/mettle/targetforce-core/pull/38))
- added ability to delete segments ([#36](https://github.com/mettle/targetforce-core/pull/36))
- handle null post content ([#34](https://github.com/mettle/targetforce-core/pull/34))

## 1.0.2 - 2020-06-22

- changed setup command success outputs to use info instead of line [#33](https://github.com/mettle/targetforce-core/pull/33))
- increased content length in the post and template tables ([#32](https://github.com/mettle/targetforce-core/pull/32))
- replaced relative URLs with route helpers when linking to the dashboard ([#29](https://github.com/mettle/targetforce-core/pull/29))
- renamed and cleaned up model factories ([#31](https://github.com/mettle/targetforce-core/pull/31))

## 1.0.1 - 2020-06-11

- fixed dashboard subscriber growth chart in PostgreSQL environments ([#15](https://github.com/mettle/targetforce-core/pull/15))
- apply correct namespace to layouts that are extended in subscriber/unsubscribe views ([#16](https://github.com/mettle/targetforce-core/pull/16))
- remove deprecated helper methods ([#17](https://github.com/mettle/targetforce-core/pull/17))
- fix database constraint error when dispatching posts in environments that use the SES provider ([#18](https://github.com/mettle/targetforce-core/pull/18))
- fix subscriber email validation rule that was failing in PostgreSQL environments ([#19](https://github.com/mettle/targetforce-core/pull/19))
- fix behaviour surrounding the dispatch of scheduled posts ([#20](https://github.com/mettle/targetforce-core/pull/20))
- fix registration toggle ([#21](https://github.com/mettle/targetforce-core/pull/21))
- fix bold text rendering in subscriber/unsubscribe views ([#27](https://github.com/mettle/targetforce-core/pull/27))

## 1.0.0 - 2020-06-09

- initial release
