import { Link } from '@inertiajs/react';
import PrimaryButton from '@/Components/PrimaryButton';

export default function PageHeader({ title, createRoute, createLabel, children }) {
    return (
        <div className="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <h1 className="text-2xl font-bold text-gray-900">{title}</h1>
            <div className="flex items-center gap-2">
                {children}
                {createRoute && (
                    <Link href={createRoute}>
                        <PrimaryButton>{createLabel}</PrimaryButton>
                    </Link>
                )}
            </div>
        </div>
    );
}
