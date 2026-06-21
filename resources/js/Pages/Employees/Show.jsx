import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import useTranslation from '@/hooks/useTranslation';
import { resourceData } from '@/utils/resource';
import { formatCurrency } from '@/utils/format';
import { Head, Link } from '@inertiajs/react';

export default function Show({ employee }) {
    const { t } = useTranslation();
    const e = resourceData(employee);

    return (
        <ErpLayout>
            <Head title={e.name} />
            <FlashMessage />
            <h1 className="mb-6 text-2xl font-bold">{e.name}</h1>

            <div className="mb-6 grid gap-4 sm:grid-cols-3">
                <div className="rounded-lg bg-white p-4 shadow"><p className="text-sm text-gray-500">{t('fields.salary')}</p><p className="text-xl font-bold">{formatCurrency(e.salary)}</p></div>
                <div className="rounded-lg bg-white p-4 shadow"><p className="text-sm text-gray-500">{t('employees.total_paid')}</p><p className="text-xl font-bold">{formatCurrency(e.total_paid)}</p></div>
                <div className="rounded-lg bg-white p-4 shadow"><p className="text-sm text-gray-500">{t('employees.remaining_salary')}</p><p className="text-xl font-bold text-amber-700">{formatCurrency(e.remaining_salary)}</p></div>
            </div>

            <div className="rounded-lg bg-white p-4 shadow text-sm">
                <dl className="grid gap-2 sm:grid-cols-2">
                    <div><dt className="text-gray-500">{t('fields.father_name')}</dt><dd>{e.father_name}</dd></div>
                    <div><dt className="text-gray-500">{t('fields.phone')}</dt><dd>{e.phone}</dd></div>
                    <div><dt className="text-gray-500">{t('fields.position')}</dt><dd>{e.position}</dd></div>
                    <div><dt className="text-gray-500">{t('fields.joining_date')}</dt><dd>{e.joining_date_shamsi}</dd></div>
                </dl>
            </div>
        </ErpLayout>
    );
}
