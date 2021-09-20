module.exports = {
    plugins: ['react', '@typescript-eslint', 'eslint-plugin-import-helpers', 'prettier'],
    extends: [
        'plugin:react/recommended',
        'plugin:@typescript-eslint/recommended',
        'plugin:react-hooks/recommended',
        'prettier',
        'plugin:prettier/recommended',
    ],
    parser: '@typescript-eslint/parser',
    parserOptions: {
        ecmaFeatures: {
            jsx: true,
            modules: true,
        },
    },
    rules: {
        semi: ['error', 'always'],
        'eol-last': ['error', 'always'],
        'no-multiple-empty-lines': ['error', {max: 1}],
        quotes: ['error', 'single', {allowTemplateLiterals: true}],
        'arrow-parens': ['error', 'always'],
        'arrow-spacing': ['error', {before: true, after: true}],
        'key-spacing': ['error', {afterColon: true}],
        'object-curly-spacing': ['error', 'never'],
        'keyword-spacing': 'error',
        'space-infix-ops': 'error',
        'max-len': ['error', {code: 120}],
        'no-trailing-spaces': 'error',
        'no-eval': 'error',
        'no-debugger': 'warn',

        'function-paren-newline': ['error', 'consistent'],
        '@typescript-eslint/naming-convention': [
            'error',
            {
                selector: 'interface',
                format: ['PascalCase'],
                custom: {
                    regex: '^I[A-Z]',
                    match: true,
                },
            },
        ],
        '@typescript-eslint/no-non-null-assertion': 1,
        '@typescript-eslint/no-var-requires': 1,
        '@typescript-eslint/no-inferrable-types': 0,
        '@typescript-eslint/explicit-function-return-type': 0,
        '@typescript-eslint/explicit-member-accessibility': 0,
        '@typescript-eslint/member-delimiter-style': 0,
        '@typescript-eslint/camelcase': 0,
        '@typescript-eslint/no-explicit-any': 0,
        '@typescript-eslint/no-use-before-define': 0,
        '@typescript-eslint/no-unused-vars': 1,
        '@typescript-eslint/no-empty-interface': 0,
        '@typescript-eslint/indent': 0,
        '@typescript-eslint/no-object-literal-type-assertion': 0,
        '@typescript-eslint/array-type': 0,
        '@typescript-eslint/member-ordering': [
            2,
            {
                default: [
                    'public-static-field',
                    'protected-static-field',
                    'private-static-field',

                    'public-static-method',
                    'protected-static-method',
                    'private-static-method',

                    'public-abstract-field',
                    'protected-abstract-field',
                    'private-abstract-field',

                    'public-instance-field',
                    'protected-instance-field',
                    'private-instance-field',

                    'public-field',
                    'protected-field',
                    'private-field',

                    'static-field',
                    'instance-field',
                    'abstract-field',

                    'field',

                    'constructor',

                    'public-abstract-method',
                    'protected-abstract-method',
                    'private-abstract-method',

                    'public-instance-method',
                    'protected-instance-method',
                    'private-instance-method',

                    'public-method',
                    'protected-method',
                    'private-method',

                    'static-method',
                    'instance-method',
                    'abstract-method',

                    'method',
                ],
            },
        ],

        'react/display-name': 0,
        'react/prop-types': 0,
        'react/jsx-key': 1,
        'react/sort-comp': [
            1,
            {
                order: ['static-variables', 'static-methods', 'variables', 'lifecycle', 'render', 'everything-else'],
                groups: {
                    variables: ['type-annotations', 'instance-variables'],
                    lifecycle: [
                        'displayName',
                        'propTypes',
                        'contextTypes',
                        'childContextTypes',
                        'mixins',
                        'statics',
                        'defaultProps',
                        'constructor',
                        'getDefaultProps',
                        'state',
                        'getInitialState',
                        'getChildContext',
                        'getDerivedStateFromProps',
                        'componentWillMount',
                        'UNSAFE_componentWillMount',
                        'componentDidMount',
                        'componentWillReceiveProps',
                        'UNSAFE_componentWillReceiveProps',
                        'shouldComponentUpdate',
                        'componentWillUpdate',
                        'UNSAFE_componentWillUpdate',
                        'getSnapshotBeforeUpdate',
                        'componentDidUpdate',
                        'componentDidCatch',
                        'componentWillUnmount',
                    ],
                },
            },
        ],
        'import-helpers/order-imports': [
            'warn',
            {
                newlinesBetween: 'always',
                groups: ['module', ['parent', 'sibling', 'index', '/^@src/']],
                alphabetize: {order: 'asc', ignoreCase: true},
            },
        ],
    },
    env: {
        es6: true,
        browser: true,
        jest: true,
    },
    settings: {
        react: {
            version: '17.0.2',
        },
    },
};
