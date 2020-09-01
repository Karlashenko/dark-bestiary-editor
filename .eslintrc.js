module.exports = {
    extends: [
        'eslint:recommended',
        'plugin:vue/essential'

    ],
    rules: {
        'vue/no-invalid-v-for': 'best practice',
        'vue/require-v-for-key': 'best practice',
        enforce: 'pre',
        test: /\.(js|vue)$/,
        loader: 'eslint-loader',
        exclude: /node_modules/
    }
}
