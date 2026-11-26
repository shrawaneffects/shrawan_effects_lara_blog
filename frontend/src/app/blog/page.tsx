'use client';

import React, { useState, useEffect, Suspense } from 'react';
import Link from 'next/link';
import { useSearchParams } from 'next/navigation';
import { formatImageUrl } from '@/lib/api';
import Sidebar from '@/components/Sidebar';

function BlogArchiveContent() {
  const searchParams = useSearchParams();
  const searchParam = searchParams.get('search') || '';
  const categoryParam = searchParams.get('category') || '';
  const sortParam = searchParams.get('sort') || 'latest';
  const pageParam = searchParams.get('page') || '1';

  const [posts, setPosts] = useState<any[]>([]);
  const [categories, setCategories] = useState<any[]>([]);
  const [tags, setTags] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [currentPage, setCurrentPage] = useState(1);
  const [lastPage, setLastPage] = useState(1);

  const [search, setSearch] = useState(searchParam);
  const [category, setCategory] = useState(categoryParam);
  const [sort, setSort] = useState(sortParam);

  useEffect(() => {
    setSearch(searchParam);
    setCategory(categoryParam);
    setSort(sortParam);
  }, [searchParam, categoryParam, sortParam]);

  useEffect(() => {
    async function loadData() {
      setLoading(true);
      const q = new URLSearchParams();
      if (searchParam) q.set('search', searchParam);
      if (categoryParam) q.set('category', categoryParam);
      if (sortParam) q.set('sort', sortParam);
      if (pageParam) q.set('page', pageParam);

      try {
        const [postsRes, catRes, tagRes] = await Promise.all([
          fetch(`/api/posts?${q.toString()}`).then(r => r.json()).catch(() => ({ data: { data: [], current_page: 1, last_page: 1 } })),
          fetch('/api/categories').then(r => r.json()).catch(() => ({ data: [] })),
          fetch('/api/tags').then(r => r.json()).catch(() => ({ data: [] })),
        ]);

        setPosts(postsRes.data?.data || []);
        setCurrentPage(postsRes.data?.current_page || 1);
        setLastPage(postsRes.data?.last_page || 1);
        setCategories(catRes.data || []);
        setTags(tagRes.data || []);
      } catch (err) {
        console.error('Error fetching blog data:', err);
      } finally {
        setLoading(false);
      }
    }
    loadData();
  }, [searchParam, categoryParam, sortParam, pageParam]);

  return (
    <div className="max-w-7xl mx-auto px-4 py-8">
      {/* Header Banner */}
      <div className="mb-8">
        <div className="text-xs font-mono text-gray-400 mb-2 flex items-center gap-2">
          <Link href="/" className="hover:text-[#00ff66]">Home</Link>
          <span>/</span>
          <span className="text-[#00ff66]">Articles</span>
        </div>
        <h1 className="font-cyber font-bold text-3xl mb-2">
          Explore <span className="text-gradient">Articles</span>
        </h1>
        <p className="text-gray-400 text-sm">
          Browse through technical tutorials, system design breakdowns, and developer guides.
        </p>
      </div>

      {/* Filter Toolbar */}
      <div className="bg-[#091206]/80 border border-[#00ff66]/20 rounded-2xl p-4 mb-8 backdrop-blur-md">
        <form method="GET" action="/blog" className="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
          <div className="sm:col-span-5 relative">
            <input
              type="text"
              name="search"
              value={search}
              onChange={(e) => setSearch(e.target.value)}
              placeholder="Search by title or topic..."
              className="w-full bg-[#050804] border border-[#00ff66]/30 rounded-xl px-4 py-2 text-xs font-mono text-white placeholder-gray-500 focus:outline-none focus:border-[#00ff66]"
            />
          </div>

          <div className="sm:col-span-3">
            <select
              name="category"
              value={category}
              onChange={(e) => setCategory(e.target.value)}
              className="w-full bg-[#050804] border border-[#00ff66]/30 rounded-xl px-3 py-2 text-xs font-mono text-white focus:outline-none focus:border-[#00ff66]"
            >
              <option value="">All Categories</option>
              {categories.map((cat: any) => (
                <option key={cat.id} value={cat.slug}>
                  {cat.name} ({cat.posts_count || 0})
                </option>
              ))}
            </select>
          </div>

          <div className="sm:col-span-2">
            <select
              name="sort"
              value={sort}
              onChange={(e) => setSort(e.target.value)}
              className="w-full bg-[#050804] border border-[#00ff66]/30 rounded-xl px-3 py-2 text-xs font-mono text-white focus:outline-none focus:border-[#00ff66]"
            >
              <option value="latest">Latest</option>
              <option value="popular">Most Viewed</option>
              <option value="oldest">Oldest</option>
            </select>
          </div>

          <div className="sm:col-span-2 flex gap-2">
            <button type="submit" className="w-full btn-gradient py-2 text-xs font-bold">
              FILTER
            </button>
            {(search || category || sort !== 'latest') && (
              <Link href="/blog" className="btn-tactical px-3 py-2 text-xs" title="Reset Filters">
                <i className="bi bi-arrow-counterclockwise"></i>
              </Link>
            )}
          </div>
        </form>
      </div>

      {/* Main Grid + Sidebar */}
      <div className="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <div className="lg:col-span-8">
          {loading ? (
            <div className="p-12 text-center text-xs font-mono text-[#00ff66]">
              <span className="cyber-beacon mr-2"></span> TELEMETRY DATASTREAM BUFFERING...
            </div>
          ) : posts.length === 0 ? (
            <div className="bg-[#091206] border border-[#00ff66]/20 rounded-2xl p-12 text-center">
              <i className="bi bi-inbox text-4xl text-[#00ff66] mb-3 inline-block"></i>
              <h4 className="font-cyber font-bold text-lg text-white mb-2">No Transmissions Found</h4>
              <p className="text-gray-400 text-xs">Try adjusting your search or category filters.</p>
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
                    {post.category && (
                      <div className="absolute top-3 left-3">
                        <span 
                          className="px-2.5 py-0.5 rounded text-[11px] font-mono text-white shadow-md"
                          style={{ backgroundColor: post.category.color || '#00ff66' }}
                        >
                          {post.category.name}
                        </span>
                      </div>
                    )}
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

                    <div className="pt-3 border-t border-[#00ff66]/15 flex items-center justify-between text-xs">
                      <div className="flex items-center gap-2">
                        <div className="w-6 h-6 rounded-full overflow-hidden bg-[#213608]">
                          <img 
                            src={formatImageUrl(post.author?.avatar)} 
                            alt={post.author?.name}
                            className="w-full h-full object-cover"
                          />
                        </div>
                        <span className="text-white text-xs font-semibold">{post.author?.name || 'Author'}</span>
                      </div>
                      <span className="text-gray-400 font-mono text-[11px]">
                        ⏱ {post.reading_time || 4}m
                      </span>
                    </div>
                  </div>
                </div>
              ))}
            </div>
          )}

          {/* Pagination */}
          {lastPage > 1 && (
            <div className="mt-10 flex items-center justify-center gap-2 font-mono text-xs">
              {Array.from({ length: lastPage }, (_, i) => i + 1).map((p) => {
                const isActive = p === currentPage;
                return (
                  <Link
                    key={p}
                    href={`/blog?page=${p}${category ? `&category=${category}` : ''}${search ? `&search=${search}` : ''}${sort ? `&sort=${sort}` : ''}`}
                    className={`w-9 h-9 rounded-xl flex items-center justify-center transition-all ${
                      isActive 
                        ? 'bg-[#00ff66] text-[#050804] font-bold shadow-lg shadow-[#00ff66]/30' 
                        : 'bg-[#091206] border border-[#00ff66]/30 text-white hover:border-[#00ff66]'
                    }`}
                  >
                    {p}
                  </Link>
                );
              })}
            </div>
          )}
        </div>

        {/* Sidebar */}
        <div className="lg:col-span-4">
          <Sidebar popularTags={tags} categories={categories} />
        </div>
      </div>
    </div>
  );
}

export default function BlogArchivePage() {
  return (
    <Suspense fallback={
      <div className="p-12 text-center text-xs font-mono text-[#00ff66]">
        INITIALIZING DATASTREAM...
      </div>
    }>
      <BlogArchiveContent />
    </Suspense>
  );
}
