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
      - branch2 # You can add multiple branches that should be sliced if a commit is pushed into the branch
    tags: [ '*.*.*' ]

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

      # set current branch or tag
      - name: Set release version for branch or tag
        run: echo "RELEASE_VERSION=${{ github.ref }}" >> $GITHUB_ENV

      - uses: erkenes/monorepo-split-action@1.3.0
        with:
          access_token: '${{ secrets.GITHUB_TOKEN }}' # The access token for the repository
          repository_protocol: 'https://' # the protocol for cloning the mono-repository
          repository_host: 'github.com' # the host of the mono-repository
          repository_organization: 'erkenes' # the organization of the mono-repository
          repository_name: 'monorepo-split' # the name of the mono-repository
          default_branch: '${{ matrix.package.default_branch }}' # default branch from the matrix
          target_branch: '${{ env.RELEASE_VERSION }}' # the target branch of the mono-repository which should be sliced (depends on the pushed branch or tag)
          package_directory: '${{ matrix.package.local_path }}' # local path of the package from the matrix
          remote_repository: '${{ matrix.package.split_repository }}' # target repository of the package from the matrix
          remote_repository_access_token: '${{ secrets.GITHUB_TOKEN }}' # an access token for the target repository (optional)
```

### Troubleshooting

#### Could not read Password

If you are getting an error like the following:

```text
fatal: could not read Password for 'https://***@github.com': No such device or address
```

To fix the issue you may have to adjust the input properties `access_token` and/or `remote_repository_access_token` by adding the prefix `x-access-token`:

```yaml
jobs:
  packages_split:
    steps:
      - uses: erkenes/monorepo-split-action@1.3.0
        with:
          access_token: 'x-access-token:${{ secrets.GITHUB_TOKEN }}'
          remote_repository_access_token: 'x-access-token:${{ secrets.GITHUB_TOKEN }}'
```

The issue can occur if you are using `tibdex/github-app-token` to generate dynamic PATs.

[See: HTTP-based Git access by an installation](https://docs.github.com/en/developers/apps/building-github-apps/authenticating-with-github-apps#http-based-git-access-by-an-installation)
