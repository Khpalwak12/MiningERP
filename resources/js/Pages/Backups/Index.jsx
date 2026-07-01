import DangerButton from '@/Components/DangerButton';
import ErpLayout from '@/Layouts/ErpLayout';
import FlashMessage from '@/Components/Erp/FlashMessage';
import PageHeader from '@/Components/Erp/PageHeader';
import PrimaryButton from '@/Components/PrimaryButton';
import SecondaryButton from '@/Components/SecondaryButton';
import useTranslation from '@/hooks/useTranslation';
import usePermission from '@/hooks/usePermission';
import { Head, router, useForm } from '@inertiajs/react';

export default function Index({ backups, backupPath, databaseDriver }) {
    const { t } = useTranslation();
    const { can } = usePermission();
    const { data, setData, post, processing, errors, reset } = useForm({
        backup_file: null,
    });

    const handleCreate = () => {
        router.post(route('backups.store'), {}, { preserveScroll: true });
    };

    const handleRestore = (filename) => {
        if (! confirm(t('backup.confirm_restore'))) {
            return;
        }

        router.post(route('backups.restore'), { filename }, { preserveScroll: true });
    };

    const handleDelete = (filename) => {
        if (! confirm(t('backup.confirm_delete'))) {
            return;
        }

        router.delete(route('backups.destroy', filename), { preserveScroll: true });
    };

    const handleUploadRestore = (e) => {
        e.preventDefault();

        if (! data.backup_file) {
            return;
        }

        if (! confirm(t('backup.confirm_restore'))) {
            return;
        }

        post(route('backups.restore'), {
            forceFormData: true,
            preserveScroll: true,
            onSuccess: () => reset('backup_file'),
        });
    };

    return (
        <ErpLayout>
            <Head title={t('backup.title')} />
            <FlashMessage />

            <PageHeader title={t('backup.title')}>
                {can('backup.create') && (
                    <PrimaryButton type="button" onClick={handleCreate}>{t('backup.create')}</PrimaryButton>
                )}
            </PageHeader>

            <div className="mb-4 rounded-lg bg-white p-4 text-sm text-gray-600 shadow">
                <p>{t('backup.description')}</p>
                <p className="mt-2"><span className="font-medium text-gray-800">{t('backup.storage_path')}:</span> {backupPath}</p>
                <p><span className="font-medium text-gray-800">{t('backup.database_driver')}:</span> {databaseDriver}</p>
            </div>

            {can('backup.restore') && (
                <form onSubmit={handleUploadRestore} className="mb-4 rounded-lg bg-white p-4 shadow">
                    <h2 className="mb-2 text-lg font-semibold">{t('backup.restore_upload')}</h2>
                    <p className="mb-3 text-sm text-gray-600">{t('backup.upload_hint')}</p>
                    <div className="flex flex-wrap items-end gap-3">
                        <input
                            type="file"
                            accept=".zip,application/zip"
                            className="block text-sm"
                            onChange={(e) => setData('backup_file', e.target.files?.[0] ?? null)}
                        />
                        <PrimaryButton type="submit" disabled={processing || !data.backup_file}>
                            {t('backup.restore')}
                        </PrimaryButton>
                    </div>
                    {errors.backup_file && <p className="mt-2 text-sm text-red-600">{errors.backup_file}</p>}
                </form>
            )}

            <div className="overflow-hidden rounded-lg bg-white shadow">
                <table className="min-w-full text-sm">
                    <thead className="bg-gray-50">
                        <tr>
                            <th className="px-4 py-3 text-left">{t('fields.date')}</th>
                            <th className="px-4 py-3 text-left">{t('backup.filename')}</th>
                            <th className="px-4 py-3 text-left">{t('backup.file_size')}</th>
                            <th className="px-4 py-3 text-right">{t('actions.column')}</th>
                        </tr>
                    </thead>
                    <tbody className="divide-y">
                        {backups.length === 0 && (
                            <tr><td colSpan={4} className="px-4 py-6 text-center text-gray-500">{t('backup.no_backups')}</td></tr>
                        )}
                        {backups.map((backup) => (
                            <tr key={backup.filename}>
                                <td className="px-4 py-3">{backup.created_at_shamsi}</td>
                                <td className="px-4 py-3 font-medium">{backup.filename}</td>
                                <td className="px-4 py-3">{backup.size_human}</td>
                                <td className="px-4 py-3 text-right">
                                    <div className="inline-flex flex-wrap justify-end gap-2">
                                        {can('backup.view') && (
                                            <a
                                                href={route('backups.download', backup.filename)}
                                                className="rounded-md border border-gray-300 px-3 py-1 text-xs font-medium text-gray-700 hover:bg-gray-50"
                                            >
                                                {t('backup.download')}
                                            </a>
                                        )}
                                        {can('backup.restore') && (
                                            <SecondaryButton type="button" onClick={() => handleRestore(backup.filename)}>
                                                {t('backup.restore')}
                                            </SecondaryButton>
                                        )}
                                        {can('backup.delete') && (
                                            <DangerButton type="button" onClick={() => handleDelete(backup.filename)}>
                                                {t('actions.delete')}
                                            </DangerButton>
                                        )}
                                    </div>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
        </ErpLayout>
    );
}
