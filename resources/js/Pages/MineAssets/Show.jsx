import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import useTranslation from '@/hooks/useTranslation';
import { formatNumber } from '@/utils/format';
import { resourceData } from '@/utils/resource';
import { Head, Link } from '@inertiajs/react';
import SecondaryButton from '@/Components/SecondaryButton';

export default function Show({ asset }) {
    const { t } = useTranslation();
    const a = resourceData(asset);

    return (
        <ErpLayout>
            <Head title={a.name} />
            <FlashMessage />
            <div className="mb-6 flex items-center justify-between">
                <h1 className="text-2xl font-bold">{a.name}</h1>
                <Link href={route('mine-assets.index')}><SecondaryButton>{t('actions.back')}</SecondaryButton></Link>
            </div>

            <div className="rounded-lg bg-white p-6 shadow text-sm">
                <dl className="grid gap-3 sm:grid-cols-2">
                    <div><dt className="text-gray-500">{t('fields.registration_date')}</dt><dd>{a.registration_date_shamsi}</dd></div>
                    <div><dt className="text-gray-500">{t('fields.status')}</dt><dd>{t(`mine_asset_statuses.${a.status}`)}</dd></div>
                    <div><dt className="text-gray-500">{t('fields.related_to')}</dt><dd>{a.related_to || '—'}</dd></div>
                    <div><dt className="text-gray-500">{t('fields.quantity')}</dt><dd>{formatNumber(a.quantity, 3)} {t(`mine_asset_units.${a.unit}`, a.unit)}</dd></div>
                    {a.remarks && (
                        <div className="sm:col-span-2"><dt className="text-gray-500">{t('fields.notes')}</dt><dd>{a.remarks}</dd></div>
                    )}
                </dl>
            </div>
        </ErpLayout>
    );
}
