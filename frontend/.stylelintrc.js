module.exports = {
    extends: ['stylelint-config-recommended', 'stylelint-config-styled-components'],
    plugins: ['stylelint-order'],
    rules: {
        'order/properties-alphabetical-order': true,
    },
};
