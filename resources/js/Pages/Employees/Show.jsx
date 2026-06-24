import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import EmployeeSalarySummary from '@/Components/Erp/EmployeeSalarySummary';
import useTranslation from '@/hooks/useTranslation';
import { resourceData } from '@/utils/resource';
import { Head, Link } from '@inertiajs/react';
import SecondaryButton from '@/Components/SecondaryButton';

export default function Show({ employee }) {
    const { t } = useTranslation();
    const e = resourceData(employee);

    return (
        <ErpLayout>
            <Head title={e.name} />
            <FlashMessage />
            <div className="mb-6 flex items-center justify-between">
                <h1 className="text-2xl font-bold">{e.name}</h1>
                <Link href={route('employees.index')}><SecondaryButton>{t('actions.back')}</SecondaryButton></Link>
            </div>

            <EmployeeSalarySummary employee={e} />

            <div className="mt-6 rounded-lg bg-white p-4 shadow text-sm">
                <h2 className="mb-3 font-semibold">{t('employees.profile_details')}</h2>
                <dl className="grid gap-2 sm:grid-cols-2">
                    <div><dt className="text-gray-500">{t('fields.father_name')}</dt><dd>{e.father_name}</dd></div>
                    <div><dt className="text-gray-500">{t('fields.phone')}</dt><dd>{e.phone}</dd></div>
                    <div><dt className="text-gray-500">{t('fields.position')}</dt><dd>{e.position}</dd></div>
                    <div><dt className="text-gray-500">{t('fields.status')}</dt><dd>{t(`statuses.${e.status}`)}</dd></div>
                </dl>
            </div>
        </ErpLayout>
    );
}
