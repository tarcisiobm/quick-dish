import en from './en';
export type LocaleType = Record<string, string>;
export type LocaleNamespace = Record<string, LocaleType>;
type Languages = 'en';

type Locales = Record<Languages, LocaleNamespace>;

const locales: Locales = {
  en
};

export default locales;
