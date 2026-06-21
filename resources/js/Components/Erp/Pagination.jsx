import { Link } from '@inertiajs/react';

/**
 * Accepts Laravel paginator props from JsonResource collections.
 * URL links live on `meta.links` (array); top-level `links` is an object.
 */
export default function Pagination({ links, meta, paginator }) {
    const source = paginator ?? { links, meta };
    const pageLinks = Array.isArray(source?.meta?.links)
        ? source.meta.links
        : Array.isArray(source?.links)
          ? source.links
          : [];

    if (pageLinks.length <= 1) {
        return null;
    }

    return (
        <div className="mt-4 flex flex-wrap gap-1">
            {pageLinks.map((link, i) => (
                <Link
                    key={i}
                    href={link.url || '#'}
                    className={`px-3 py-1 text-sm rounded border ${
                        link.active
                            ? 'bg-indigo-600 text-white border-indigo-600'
                            : link.url
                              ? 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
                              : 'bg-gray-100 text-gray-400 border-gray-200 cursor-not-allowed'
                    }`}
                    dangerouslySetInnerHTML={{ __html: link.label }}
                    preserveState
                />
            ))}
        </div>
    );
}
