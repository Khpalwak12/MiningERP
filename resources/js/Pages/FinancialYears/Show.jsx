import ActionButtons from '@/Components/Erp/ActionButtons';
import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import useTranslation from '@/hooks/useTranslation';
import usePermission from '@/hooks/usePermission';
import { resourceData } from '@/utils/resource';
import { Head, router } from '@inertiajs/react';
import PrimaryButton from '@/Components/PrimaryButton';

export default function Show({ financialYear }) {
    const { t } = useTranslation();
    const { can } = usePermission();
    const year = resourceData(financialYear);

    const handleActivate = () => {
        const message = t('financial_years.confirm_activate').replace(':name', year.name);
        if (confirm(message)) {
            router.post(route('financial-years.activate', year.id));
        }
    };

    return (
        <ErpLayout>
            <Head title={t('financial_years.show')} />
            <FlashMessage />
            <div className="mb-6 flex items-center justify-between">
                <h1 className="text-2xl font-bold">{t('financial_years.show')} — {year.name}</h1>
                <div className="flex items-center gap-2">
                    {year.status !== 'active' && can('financial-year.activate') && (
                        <PrimaryButton type="button" onClick={handleActivate}>{t('financial_years.activate')}</PrimaryButton>
                    )}
                    <ActionButtons
                        editHref={year.status === 'active' && can('financial-year.edit') ? route('financial-years.edit', year.id) : null}
                    />
                </div>
            </div>
            <div className="rounded-lg bg-white p-6 shadow text-sm">
                <dl className="grid gap-3 sm:grid-cols-2">
                    <div><dt className="text-gray-500">{t('fields.year_name')}</dt><dd>{year.name}</dd></div>
                    <div><dt className="text-gray-500">{t('fields.status')}</dt><dd>{t(`financial_years.statuses.${year.status}`)}</dd></div>
                    <div><dt className="text-gray-500">{t('fields.start_date')}</dt><dd>{year.start_date_shamsi}</dd></div>
                    <div><dt className="text-gray-500">{t('fields.end_date')}</dt><dd>{year.end_date_shamsi}</dd></div>
                    {year.notes && <div className="sm:col-span-2"><dt className="text-gray-500">{t('fields.notes')}</dt><dd>{year.notes}</dd></div>}
                </dl>
            </div>
        </ErpLayout>
    );
}
