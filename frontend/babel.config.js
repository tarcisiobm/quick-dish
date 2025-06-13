export default {
  presets: [
    ['@babel/preset-env', { modules: false }],
    '@babel/preset-typescript'
  ],
  plugins: [
    '@babel/plugin-transform-runtime',
    '@babel/plugin-syntax-dynamic-import'
  ]
};

