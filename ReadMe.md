# GitHub Action for Monorepo Split

Do you have a monorepo project on GitHub and need to split packages into many repositories?
Add this GitHub Action to your workflow and let it split your packages on every push to the main (or another) branch.


## Define your GitHub Workflow

```yaml
name: 'Split Packages'

on:
  push:
    branches:
      - main

env:
  GITHUB_TOKEN: ${{ secrets.GITHUB_TOKEN }}

jobs:
  packages_split:
    runs-on: ubuntu-latest

    strategy:
      fail-fast: false
      matrix:
        # define package to repository map
        package:
          - local_path: 'LocalPackages/Package.One' # the local path to the package which should be separated
            split_repository: 'https://github.com/erkenes/package-one.git' # the target repository where the changed should be pushed
            default_branch: 'main' # the default branch of the package (most it is `main`)
#          - local_path: 'LocalPackages/Package.Two'
#            split_repository: 'https://github.com/erkenes/package-two.git'
#            default_branch: main

    steps:
      - uses: actions/checkout@v2

      # no tag
      - if: "!startsWith(github.ref, 'refs/tags/')"
        name: Set release version for branch
        run: echo "RELEASE_VERSION=refs/heads/${GITHUB_REF#refs/*/}" >> $GITHUB_ENV

      # with tag
      - if: "startsWith(github.ref, 'refs/tags/')"
        name: Set release version for tag
        run: echo "RELEASE_VERSION=refs/tags/${GITHUB_REF#refs/*/}" >> $GITHUB_ENV

      - uses: erkenes/monorepo-split-action@1.0.0
        with:
          repository_protocol: 'https://' # the protocol for cloning the mono-repository
          repository_host: 'github.com' # the host of the mono-repository
          repository_organization: 'erkenes' # the organization of the mono-repository
          repository_name: 'monorepo-split' # the name of the mono-repository
          allowed_refs_pattern: /^refs\\/(tags|heads)\\/([0-9]+\\.[0-9]+(\\.[0-9]+)?|main)$/ # which heads (branches/tags) should be sliced
          default_branch: '${{ matrix.package.default_branch }}' # default branch from the matrix
          target_branch: '${{ env.RELEASE_VERSION }}' # the target branch of the mono-repository which should be sliced (depends on the pushed branch or tag)
          package_directory: '${{ matrix.package.local_path }}' # local path of the package from the matrix
          remote_repository: '${{ matrix.package.split_repository }}' # target repository of the package from the matrix
          remote_repository_access_token: '${{ secrets.GITHUB_TOKEN }}' # an access token for the target repository
```
