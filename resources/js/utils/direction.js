export const RTL_LOCALES = ['ps'];

export function isRtlLocale(locale) {
    return RTL_LOCALES.includes(locale);
}

export function getDocumentDirection(locale) {
    return isRtlLocale(locale) ? 'rtl' : 'ltr';
}

export function getDocumentLang(locale) {
    return locale === 'ps' ? 'ps' : 'en';
}

export function syncDocumentDirection(locale = 'en') {
    document.documentElement.setAttribute('dir', getDocumentDirection(locale));
    document.documentElement.setAttribute('lang', getDocumentLang(locale));
}
