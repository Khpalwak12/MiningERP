import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import PageHeader from '@/Components/Erp/PageHeader';
import Pagination from '@/Components/Erp/Pagination';
import useTranslation from '@/hooks/useTranslation';
import { Head, router } from '@inertiajs/react';
import { useState } from 'react';
import TextInput from '@/Components/TextInput';
import InputLabel from '@/Components/InputLabel';
import PrimaryButton from '@/Components/PrimaryButton';

export default function Index({ logs = {}, filters = {} }) {
    const { t } = useTranslation();
    const [search, setSearch] = useState(filters.search ?? '');
    const [dateFrom, setDateFrom] = useState(filters.date_from ?? '');
    const [dateTo, setDateTo] = useState(filters.date_to ?? '');

    const rows = Array.isArray(logs.data) ? logs.data : [];

    const submitFilters = (e) => {
        e.preventDefault();
        router.get(
            route('activity-logs.index'),
            { search, date_from: dateFrom, date_to: dateTo },
            { preserveState: true, replace: true },
        );
    };

    return (
        <ErpLayout>
            <Head title={t('activity_logs.title')} />
            <FlashMessage />
            <PageHeader title={t('activity_logs.title')} />

            <form onSubmit={submitFilters} className="mb-4 flex flex-wrap items-end gap-4 rounded-lg bg-white p-4 shadow">
                <div className="min-w-[12rem]">
                    <InputLabel value={t('fields.search')} />
                    <TextInput
                        value={search}
                        onChange={(e) => setSearch(e.target.value)}
                        placeholder={t('fields.search')}
                        className="mt-1 block w-full"
                    />
                </div>
                <div>
                    <InputLabel value={t('fields.date_from')} />
                    <TextInput
                        value={dateFrom}
                        onChange={(e) => setDateFrom(e.target.value)}
                        placeholder="1404/01/01"
                        className="mt-1 block w-full"
                    />
                </div>
                <div>
                    <InputLabel value={t('fields.date_to')} />
                    <TextInput
                        value={dateTo}
                        onChange={(e) => setDateTo(e.target.value)}
                        placeholder="1404/12/29"
                        className="mt-1 block w-full"
                    />
                </div>
                <PrimaryButton type="submit">{t('actions.search')}</PrimaryButton>
            </form>

            <div className="overflow-hidden rounded-lg bg-white shadow">
                <table className="min-w-full text-sm">
                    <thead className="bg-gray-50">
                        <tr>
                            <th className="px-4 py-3 text-left">{t('fields.date')}</th>
                            <th className="px-4 py-3 text-left">{t('activity_logs.event')}</th>
                            <th className="px-4 py-3 text-left">{t('fields.description')}</th>
                            <th className="px-4 py-3 text-left">{t('activity_logs.causer')}</th>
                        </tr>
                    </thead>
                    <tbody className="divide-y">
                        {rows.length === 0 && (
                            <tr>
                                <td colSpan={4} className="px-4 py-6 text-center text-gray-500">
                                    {t('messages.no_records')}
                                </td>
                            </tr>
                        )}
                        {rows.map((log) => (
                            <tr key={log.id}>
                                <td className="px-4 py-3">{log.created_at_shamsi || ''}</td>
                                <td className="px-4 py-3">{log.log_name || ''}</td>
                                <td className="px-4 py-3">{log.description || ''}</td>
                                <td className="px-4 py-3">{log.causer_name || ''}</td>
                            </tr>
                        ))}
                    </tbody>
                </table>
                <div className="p-4">
                    <Pagination paginator={logs} />
                </div>
            </div>
        </ErpLayout>
    );
}
