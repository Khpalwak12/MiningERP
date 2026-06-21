import { router } from '@inertiajs/react';
import useTranslation from '@/hooks/useTranslation';

export default function LocaleSwitcher() {
    const { locale, t } = useTranslation();

    const switchLocale = (newLocale) => {
        router.post(route('locale.update'), { locale: newLocale }, { preserveScroll: true });
    };

    return (
        <div className="flex gap-1 rounded-md border border-gray-200 p-0.5 text-xs">
            <button
                type="button"
                onClick={() => switchLocale('en')}
                className={`rounded px-2 py-1 ${locale === 'en' ? 'bg-indigo-600 text-white' : 'text-gray-600 hover:bg-gray-50'}`}
            >
                EN
            </button>
            <button
                type="button"
                onClick={() => switchLocale('ps')}
                className={`rounded px-2 py-1 ${locale === 'ps' ? 'bg-indigo-600 text-white' : 'text-gray-600 hover:bg-gray-50'}`}
            >
                PS
            </button>
        </div>
    );
}
