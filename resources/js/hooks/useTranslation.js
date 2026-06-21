import { usePage } from '@inertiajs/react';

export default function useTranslation() {
    const { locale, translations } = usePage().props;

    const t = (key, fallback = key) => {
        const parts = key.split('.');
        let value = translations?.[locale] ?? translations?.en ?? {};

        for (const part of parts) {
            value = value?.[part];
            if (value === undefined) {
                return fallback;
            }
        }

        return value;
    };

    return { t, locale, isRtl: locale === 'ps' };
}
