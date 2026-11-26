import React from 'react';
import Link from 'next/link';
import { fetchApi, formatImageUrl } from '@/lib/api';
import Sidebar from '@/components/Sidebar';
import staticRoutes from '@/data/static-routes.json';

export async function generateStaticParams() {
  return staticRoutes.tags.map((slug: string) => ({ slug }));
}

export default async function TagArchivePage({
  params,
}: {
  params: Promise<{ slug: string }>;
}) {
  const { slug } = await params;
  const postsData = await fetchApi(`/posts?tag=${slug}`);
  const categories = await fetchApi('/categories') || [];
  const tags = await fetchApi('/tags') || [];

  const posts = postsData?.data || [];

  return (
    <div className="max-w-7xl mx-auto px-4 py-8">
      {/* Breadcrumb */}
      <div className="text-xs font-mono text-gray-400 mb-4 flex items-center gap-2">
        <Link href="/" className="hover:text-[#00ff66]">Home</Link>
        <span>/</span>
        <Link href="/blog" className="hover:text-[#00ff66]">Articles</Link>
        <span>/</span>
        <span className="text-[#00ff66]">#{slug}</span>
      </div>

      <div className="mb-8">
        <h1 className="font-cyber font-bold text-3xl mb-2">
          Tag: <span className="text-gradient">#{slug}</span>
        </h1>
        <p className="text-gray-400 text-sm">
          Telemetry transmissions tagged with #{slug} ({posts.length} results).
        </p>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <div className="lg:col-span-8">
          {posts.length === 0 ? (
            <div className="bg-[#091206] border border-[#00ff66]/20 rounded-2xl p-12 text-center">
              <h4 className="font-cyber font-bold text-white mb-2">No Articles Tagged #{slug}</h4>
            </div>
          ) : (
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-6">
              {posts.map((post: any) => (
                <div key={post.id} className="card-post flex flex-col group">
                  <div className="relative aspect-[16/10] overflow-hidden">
                    <img
                      src={formatImageUrl(post.featured_image)}
                      alt={post.title}
                      className="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                    />
                  </div>
                  <div className="p-5 flex flex-col flex-grow justify-between">
                    <div>
                      <h4 className="font-cyber font-bold text-base leading-snug group-hover:text-[#00ff66] transition-colors mb-2">
                        <Link href={`/blog/${post.slug}`}>
                          {post.title}
                        </Link>
                      </h4>
                      <p className="text-gray-400 text-xs line-clamp-2 mb-4 leading-relaxed">
                        {post.excerpt}
                      </p>
                    </div>

                    <div className="pt-3 border-t border-[#00ff66]/15 flex items-center justify-between text-xs font-mono text-gray-400">
                      <span>{new Date(post.published_at || post.created_at).toLocaleDateString()}</span>
                      <span>⏱ {post.reading_time || 4}m</span>
                    </div>
                  </div>
                </div>
              ))}
            </div>
          )}
        </div>

        <div className="lg:col-span-4">
          <Sidebar popularTags={tags} categories={categories} />
        </div>
      </div>
    </div>
  );
}
