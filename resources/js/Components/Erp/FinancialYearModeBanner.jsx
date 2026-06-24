import { usePage } from '@inertiajs/react';
import useTranslation from '@/hooks/useTranslation';
import { resourceData } from '@/utils/resource';

export default function FinancialYearModeBanner({ className = 'mb-4' }) {
    const { t } = useTranslation();
    const { isAllYearsMode, activeFinancialYear } = usePage().props;
    const activeYear = resourceData(activeFinancialYear);

    if (isAllYearsMode) {
        return (
            <div className={`rounded-lg border border-amber-300 bg-amber-50 px-4 py-3 text-sm text-amber-900 ${className}`}>
                <p className="font-medium">{t('financial_years.current_mode_all')}</p>
                <p className="mt-1">{t('financial_years.all_years_read_only')}</p>
            </div>
        );
    }

    if (activeYear?.display_name || activeYear?.name) {
        const label = t('financial_years.current_mode_year').replace(':name', activeYear.display_name || activeYear.name);

        return (
            <div className={`rounded-lg border border-indigo-200 bg-indigo-50 px-4 py-3 text-sm text-indigo-900 ${className}`}>
                {label}
            </div>
        );
    }

    return null;
}
