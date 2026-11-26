import React from 'react';
import Link from 'next/link';
import { notFound } from 'next/navigation';
import { fetchApi, formatImageUrl } from '@/lib/api';
import Sidebar from '@/components/Sidebar';
import type { Metadata } from 'next';
import staticRoutes from '@/data/static-routes.json';

export async function generateStaticParams() {
  return staticRoutes.posts.map((slug: string) => ({ slug }));
}

export async function generateMetadata({
  params,
}: {
  params: Promise<{ slug: string }>;
}): Promise<Metadata> {
  const { slug } = await params;
  const data = await fetchApi(`/posts/${slug}`);
  const post = data?.post;
  if (!post) return { title: 'Post Not Found | Shrawan Effects' };

  return {
    title: `${post.meta_title || post.title} | Shrawan Effects`,
    description: post.meta_description || post.excerpt,
    openGraph: {
      title: post.title,
      description: post.excerpt,
      images: [formatImageUrl(post.featured_image)],
    },
  };
}

export default async function BlogPostPage({
  params,
}: {
  params: Promise<{ slug: string }>;
}) {
  const { slug } = await params;
  const data = await fetchApi(`/posts/${slug}`);
  if (!data?.post) notFound();

  const { post, related_posts, prev_post, next_post } = data;
  const categories = await fetchApi('/categories') || [];
  const tags = await fetchApi('/tags') || [];

  return (
    <div className="max-w-7xl mx-auto px-4 py-8">
      {/* Breadcrumb */}
      <div className="text-xs font-mono text-gray-400 mb-4 flex items-center gap-2">
        <Link href="/" className="hover:text-[#00ff66]">Home</Link>
        <span>/</span>
        <Link href="/blog" className="hover:text-[#00ff66]">Articles</Link>
        <span>/</span>
        {post.category && (
          <>
            <Link href={`/category/${post.category.slug}`} className="hover:text-[#00ff66]">
              {post.category.name}
            </Link>
            <span>/</span>
          </>
        )}
        <span className="text-gray-300 truncate max-w-xs">{post.title}</span>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-12 gap-8">
        {/* Post Content (8 cols) */}
        <article className="lg:col-span-8">
          {/* Header Info */}
          <div className="mb-6">
            <div className="flex items-center gap-2 mb-3">
              {post.category && (
                <span 
                  className="px-3 py-1 rounded-md text-xs font-mono text-white"
                  style={{ backgroundColor: post.category.color || '#4c00ff' }}
                >
                  {post.category.name}
                </span>
              )}
              {post.is_featured && (
                <span className="priority-intel-badge text-xs">
                  ★ FEATURED
                </span>
              )}
            </div>

            <h1 className="font-cyber font-bold text-3xl sm:text-4xl leading-tight text-white mb-4">
              {post.title}
            </h1>

            {/* Author & Meta Row */}
            <div className="flex flex-wrap items-center justify-between gap-4 py-4 border-y border-[#00ff66]/20 text-xs font-mono text-gray-400">
              <div className="flex items-center gap-3">
                <div className="w-10 h-10 rounded-full overflow-hidden bg-[#213608] border border-[#00ff66]/30">
                  <img 
                    src={formatImageUrl(post.author?.avatar)} 
                    alt={post.author?.name}
                    className="w-full h-full object-cover"
                  />
                </div>
                <div>
                  <div className="font-bold text-white text-sm">{post.author?.name || 'Shrawan Choudhary'}</div>
                  <div className="text-gray-500 text-[11px]">
                    {new Date(post.published_at || post.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}
                  </div>
                </div>
              </div>

              <div className="flex items-center gap-4">
                <span><i className="bi bi-clock text-[#00ff66]"></i> {post.reading_time || 4}m read</span>
                <span><i className="bi bi-eye text-[#00ff66]"></i> {post.views_count || 1} views</span>
              </div>
            </div>
          </div>

          {/* Featured Image */}
          {post.featured_image && (
            <div className="rounded-2xl overflow-hidden mb-8 border border-[#00ff66]/20 aspect-[16/9]">
              <img
                src={formatImageUrl(post.featured_image)}
                alt={post.title}
                className="w-full h-full object-cover"
              />
            </div>
          )}

          {/* Post Body (rendered HTML) */}
          <div 
            className="post-content text-gray-300 leading-relaxed space-y-4 mb-10"
            dangerouslySetInnerHTML={{ __html: post.content }}
          />

          {/* Tags */}
          {post.tags && post.tags.length > 0 && (
            <div className="flex flex-wrap items-center gap-2 py-4 border-t border-[#00ff66]/20 mb-8">
              <span className="text-xs font-mono text-gray-400 mr-2">Tags:</span>
              {post.tags.map((tag: any) => (
                <Link
                  key={tag.id}
                  href={`/tag/${tag.slug}`}
                  className="font-mono text-xs px-2.5 py-1 rounded bg-[#213608]/40 hover:bg-[#00ff66] hover:text-[#050804] text-[#00ff66] border border-[#00ff66]/30 transition-all"
                >
                  #{tag.name}
                </Link>
              ))}
            </div>
          )}

          {/* Author Box */}
          <div className="bg-[#091206] border border-[#00ff66]/30 rounded-2xl p-6 mb-8 flex flex-col sm:flex-row items-center sm:items-start gap-4">
            <div className="w-16 h-16 rounded-full overflow-hidden bg-[#213608] border-2 border-[#00ff66] flex-shrink-0">
              <img 
                src={formatImageUrl(post.author?.avatar)} 
                alt={post.author?.name}
                className="w-full h-full object-cover"
              />
            </div>
            <div>
              <div className="text-xs font-mono text-[#00ff66] uppercase mb-1">Written by</div>
              <h4 className="font-cyber font-bold text-lg text-white mb-2">{post.author?.name || 'Shrawan Choudhary'}</h4>
              <p className="text-gray-400 text-xs leading-relaxed">
                {post.author?.bio || 'Senior Full-Stack Architect & Founder at Shrawan Effects with extensive experience building modern web platforms.'}
              </p>
            </div>
          </div>

          {/* Prev / Next Navigation */}
          <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-10">
            {prev_post && (
              <Link href={`/blog/${prev_post.slug}`} className="bg-[#091206] border border-[#00ff66]/20 rounded-xl p-4 hover:border-[#00ff66] transition-all group">
                <div className="text-[11px] font-mono text-[#00ff66] mb-1">// PREV TRANSMISSION</div>
                <div className="font-bold text-xs text-white group-hover:text-[#00ff66] transition-colors line-clamp-1">
                  {prev_post.title}
                </div>
              </Link>
            )}
            {next_post && (
              <Link href={`/blog/${next_post.slug}`} className="bg-[#091206] border border-[#00ff66]/20 rounded-xl p-4 hover:border-[#00ff66] transition-all group sm:text-right">
                <div className="text-[11px] font-mono text-[#00ff66] mb-1">// NEXT TRANSMISSION</div>
                <div className="font-bold text-xs text-white group-hover:text-[#00ff66] transition-colors line-clamp-1">
                  {next_post.title}
                </div>
              </Link>
            )}
          </div>

          {/* You Might Also Like */}
          {related_posts && related_posts.length > 0 && (
            <div className="mb-10">
              <h3 className="font-cyber font-bold text-lg text-white mb-4 flex items-center gap-2">
                <i className="bi bi-cpu text-[#00ff66]"></i> You Might Also Like
              </h3>
              <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
                {related_posts.map((rPost: any) => (
                  <Link key={rPost.id} href={`/blog/${rPost.slug}`} className="card-post p-3 group">
                    <div className="aspect-[16/10] rounded-lg overflow-hidden mb-2">
                      <img
                        src={formatImageUrl(rPost.featured_image)}
                        alt={rPost.title}
                        className="w-full h-full object-cover group-hover:scale-105 transition-transform"
                      />
                    </div>
                    <h5 className="font-bold text-xs text-white group-hover:text-[#00ff66] transition-colors line-clamp-2 mb-1">
                      {rPost.title}
                    </h5>
                    <span className="text-[10px] font-mono text-gray-400">
                      {rPost.reading_time || 4} min read
                    </span>
                  </Link>
                ))}
              </div>
            </div>
          )}

          {/* Comments Section */}
          <div className="bg-[#091206] border border-[#00ff66]/20 rounded-2xl p-6">
            <h3 className="font-cyber font-bold text-lg text-white mb-4 flex items-center gap-2">
              <i className="bi bi-chat-square-dots text-[#00ff66]"></i> Discussion Datastream
            </h3>

            {post.approved_comments && post.approved_comments.length > 0 ? (
              <div className="space-y-4 mb-6">
                {post.approved_comments.map((comment: any) => (
                  <div key={comment.id} className="p-4 rounded-xl bg-[#050804] border border-[#00ff66]/15">
                    <div className="flex items-center justify-between mb-2">
                      <span className="font-bold text-xs text-[#00ff66]">{comment.name}</span>
                      <span className="text-[10px] font-mono text-gray-500">
                        {new Date(comment.created_at).toLocaleDateString()}
                      </span>
                    </div>
                    <p className="text-gray-300 text-xs leading-relaxed">{comment.content}</p>
                  </div>
                ))}
              </div>
            ) : (
              <p className="text-gray-400 text-xs mb-6 font-mono">
                // No transmissions logged in this stream yet. Be the first to deploy an observation.
              </p>
            )}

            {/* Leave Comment Form */}
            <form action={`/api/posts/${post.slug}/comments`} method="POST" className="space-y-3">
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <input
                  type="text"
                  name="name"
                  placeholder="Your Name *"
                  required
                  className="bg-[#050804] border border-[#00ff66]/30 rounded-xl px-3 py-2 text-xs font-mono text-white placeholder-gray-500 focus:outline-none focus:border-[#00ff66]"
                />
                <input
                  type="email"
                  name="email"
                  placeholder="Your Email *"
                  required
                  className="bg-[#050804] border border-[#00ff66]/30 rounded-xl px-3 py-2 text-xs font-mono text-white placeholder-gray-500 focus:outline-none focus:border-[#00ff66]"
                />
              </div>
              <textarea
                name="content"
                rows={4}
                placeholder="Observation Comment *"
                required
                className="w-full bg-[#050804] border border-[#00ff66]/30 rounded-xl px-3 py-2 text-xs font-mono text-white placeholder-gray-500 focus:outline-none focus:border-[#00ff66]"
              />
              <button type="submit" className="btn-gradient text-xs py-2 px-6">
                TRANSMIT COMMENT
              </button>
            </form>
          </div>
        </article>

        {/* Sidebar (4 cols) */}
        <div className="lg:col-span-4">
          <Sidebar popularTags={tags} categories={categories} />
        </div>
      </div>
    </div>
  );
}
