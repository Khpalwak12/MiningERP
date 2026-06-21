import { Link } from '@inertiajs/react';
import { Download, Eye, Pencil, Printer, Trash2 } from 'lucide-react';
import useTranslation from '@/hooks/useTranslation';

const VARIANTS = {
    view: {
        icon: Eye,
        className: 'text-sky-600 hover:bg-sky-50 focus:ring-sky-500',
    },
    edit: {
        icon: Pencil,
        className: 'text-indigo-600 hover:bg-indigo-50 focus:ring-indigo-500',
    },
    delete: {
        icon: Trash2,
        className: 'text-red-600 hover:bg-red-50 focus:ring-red-500',
    },
    print: {
        icon: Printer,
        className: 'text-gray-600 hover:bg-gray-100 focus:ring-gray-500',
    },
    download: {
        icon: Download,
        className: 'text-green-600 hover:bg-green-50 focus:ring-green-500',
    },
};

const baseButtonClass =
    'inline-flex h-8 w-8 items-center justify-center rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-offset-1';

function Tooltip({ label, children }) {
    return (
        <span className="group relative inline-flex">
            {children}
            <span
                className="pointer-events-none absolute bottom-full left-1/2 z-50 mb-1.5 -translate-x-1/2 whitespace-nowrap rounded bg-gray-900 px-2 py-1 text-xs text-white opacity-0 shadow transition-opacity group-hover:opacity-100 group-focus-within:opacity-100"
                role="tooltip"
            >
                {label}
            </span>
        </span>
    );
}

function ActionIconButton({ variant, label, href, onClick, external = false }) {
    const { icon: Icon, className } = VARIANTS[variant];
    const buttonClass = `${baseButtonClass} ${className}`;

    const content = <Icon className="h-4 w-4" strokeWidth={2} aria-hidden="true" />;

    if (href) {
        if (external) {
            return (
                <Tooltip label={label}>
                    <a href={href} className={buttonClass} aria-label={label}>
                        {content}
                    </a>
                </Tooltip>
            );
        }

        return (
            <Tooltip label={label}>
                <Link href={href} className={buttonClass} aria-label={label}>
                    {content}
                </Link>
            </Tooltip>
        );
    }

    return (
        <Tooltip label={label}>
            <button type="button" onClick={onClick} className={buttonClass} aria-label={label}>
                {content}
            </button>
        </Tooltip>
    );
}

export default function ActionButtons({
    viewHref,
    editHref,
    onDelete,
    deleteConfirm,
    printHref,
    downloadHref,
    printLabel,
    downloadLabel,
    className = '',
}) {
    const { t } = useTranslation();

    const handleDelete = () => {
        const message = deleteConfirm ?? t('messages.confirm_delete');

        if (confirm(message)) {
            onDelete?.();
        }
    };

    const items = [
        viewHref ? { variant: 'view', href: viewHref, label: t('actions.view') } : null,
        editHref ? { variant: 'edit', href: editHref, label: t('actions.edit') } : null,
        onDelete ? { variant: 'delete', onClick: handleDelete, label: t('actions.delete') } : null,
        downloadHref
            ? {
                variant: 'download',
                href: downloadHref,
                label: downloadLabel ?? t('actions.download'),
                external: true,
            }
            : null,
        printHref
            ? {
                variant: 'print',
                href: printHref,
                label: printLabel ?? t('actions.print'),
                external: true,
            }
            : null,
    ].filter(Boolean);

    if (items.length === 0) {
        return null;
    }

    return (
        <div className={`inline-flex items-center justify-end gap-1 ${className}`}>
            {items.map((item) => (
                <ActionIconButton key={item.variant} {...item} />
            ))}
        </div>
    );
}
