import en from './en'
export type LocaleType = Record<string, string>;
type Languages = 'en';

type Locales = Record<Languages, LocaleType>;

const locales: Locales = {
  en
}

export default locales;
