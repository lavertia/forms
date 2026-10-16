# Lavertia Forms

**Experimental v0.2 package, no stable release.**

Lavertia Forms describes composable forms and fields as PHP components. The standalone PHP implementation is under review in [PR #2](https://github.com/lavertia/forms/pull/2). The `main` branch is a repository scaffold until that PR is verified and merged.

Forms do **not** persist request data on their own. The consuming Laravel application must enforce authentication, CSRF, Form Requests, Policies and allowlisted persistence.

See the [Lavertia documentation](https://lavertia.github.io/lavertia-doc/), [release-readiness issue](https://github.com/lavertia/lavertia/issues/3), and [security policy](SECURITY.md).

**License:** MIT; see [LICENSE.md](LICENSE.md). A public source repository is not a production-ready Composer release.
