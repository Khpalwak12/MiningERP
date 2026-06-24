import useTranslation from '@/hooks/useTranslation';
import { resourceItems } from '@/utils/resource';

export default function FinancialYearSelect({ value, onChange, financialYears, className = '' }) {
    const { t } = useTranslation();
    const options = resourceItems(financialYears);

    return (
        <div className={className}>
            <label className="block text-sm font-medium text-gray-700">{t('fields.financial_year')}</label>
            <select
                className="mt-1 rounded-md border-gray-300 text-sm"
                value={value || ''}
                onChange={(e) => onChange(e.target.value)}
            >
                <option value="">{t('actions.filter')} — {t('fields.financial_year')}</option>
                {options.map((year) => (
                    <option key={year.id} value={year.id}>{year.name}</option>
                ))}
            </select>
        </div>
    );
}
