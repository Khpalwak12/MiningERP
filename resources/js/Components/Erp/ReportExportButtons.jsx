import ActionButtons from '@/Components/Erp/ActionButtons';
import usePermission from '@/hooks/usePermission';
import useTranslation from '@/hooks/useTranslation';

export default function ReportExportButtons({ exportType, queryString = '' }) {
    const { can } = usePermission();
    const { t } = useTranslation();

    if (!can('reports.export') || !exportType) {
        return null;
    }

    const suffix = queryString ? `?${queryString}` : '';

    return (
        <ActionButtons
            downloadHref={route('reports.export.excel', exportType) + suffix}
            downloadLabel={t('actions.export_excel')}
            printHref={route('reports.export.pdf', exportType) + suffix}
            printLabel={t('actions.export_pdf')}
        />
    );
}
