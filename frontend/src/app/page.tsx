import React from 'react';
import Link from 'next/link';
import { fetchApi, formatImageUrl } from '@/lib/api';
import Sidebar from '@/components/Sidebar';

export default async function HomePage() {
  const homeData = await fetchApi('/home');
  const leadPost = homeData?.featured_lead;
  const secondaryPosts = homeData?.featured_secondary || [];
  const trendingPosts = homeData?.trending_posts || [];
  const categories = homeData?.categories || [];
  const latestPosts = homeData?.latest_posts || [];
  const popularTags = homeData?.popular_tags || [];

  return (
    <div>
      {/* 1. System Status Ticker */}
      <div className="max-w-7xl mx-auto px-4 pt-4">
        <div className="hud-ticker px-4 py-2 flex flex-col sm:flex-row items-center justify-between text-xs gap-2">
          <div className="flex items-center gap-2">
            <span className="cyber-beacon" />
            <span className="font-mono text-gray-300">
              // SYSTEM: <span className="text-[#00ff66]">ACTIVE</span> | NODE: SECURE_CORE_01 | PROTOCOL: TLS 1.3 / HTTP/2.0
            </span>
          </div>
          <div className="flex items-center gap-4 font-mono text-gray-400">
            <span>LATENCY: <strong className="text-[#00ff66]">12ms</strong></span>
            <span><strong className="text-[#00ff66]">100%</strong> OPERATIONAL</span>
          </div>
        </div>
      </div>

      {/* 2. Hero Section / Transmission Featured Intel */}
      {leadPost && (
        <section className="max-w-7xl mx-auto px-4 py-8">
          <div className="flex items-center gap-2 mb-4">
            <span className="telemetry-tag">
              <i className="bi bi-broadcast"></i> TRANSMISSION // FEATURED INTEL
            </span>
          </div>

          <div className="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
            {/* Bento Lead (7 cols) */}
            <div className="lg:col-span-7">
              <div className="card-post h-full flex flex-col relative group">
                <div className="relative aspect-[16/10] overflow-hidden">
                  <img
                    src={formatImageUrl(leadPost.featured_image)}
                    alt={leadPost.title}
                    className="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                  />
                  <div className="absolute top-3 right-3">
                    <span className="priority-intel-badge flex items-center gap-1.5">
                      <i className="bi bi-star-fill text-warning"></i> PRIORITY INTEL
                    </span>
                  </div>
                </div>

                <div className="p-6 flex flex-col flex-grow justify-between">
                  <div>
                    {leadPost.category && (
                      <Link 
                        href={`/category/${leadPost.category.slug}`}
                        className="inline-block px-3 py-1 rounded-md text-xs font-mono font-semibold mb-3 text-white"
                        style={{ backgroundColor: leadPost.category.color || '#4c00ff' }}
                      >
                        {leadPost.category.name}
                      </Link>
                    )}
                    <h2 className="font-cyber font-bold text-2xl mb-2 group-hover:text-[#00ff66] transition-colors">
                      <Link href={`/blog/${leadPost.slug}`}>
                        {leadPost.title}
                      </Link>
                    </h2>
                    <p className="text-gray-400 text-sm line-clamp-2 leading-relaxed">
                      {leadPost.excerpt}
                    </p>
                  </div>

                  <div className="pt-4 mt-4 border-t border-[#00ff66]/15 flex items-center justify-between text-xs">
                    <div className="flex items-center gap-3">
                      <div className="w-8 h-8 rounded-full overflow-hidden bg-[#213608] border border-[#00ff66]/30">
                        <img 
                          src={formatImageUrl(leadPost.author?.avatar)} 
                          alt={leadPost.author?.name || 'Author'}
                          className="w-full h-full object-cover"
                        />
                      </div>
                      <div>
                        <div className="font-bold text-white">{leadPost.author?.name || 'Shrawan Choudhary'}</div>
                        <div className="text-gray-500 font-mono text-[11px]">
                          {new Date(leadPost.published_at || leadPost.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}
                        </div>
                      </div>
                    </div>
                    <span className="font-mono text-[#00ff66] text-xs">
                      // {leadPost.reading_time || 4} MIN READ
                    </span>
                  </div>
                </div>
              </div>
            </div>

            {/* Bento Secondary (5 cols) */}
            <div className="lg:col-span-5 flex flex-col gap-6">
              {secondaryPosts.map((post: any) => (
                <div key={post.id} className="card-post flex-1 flex flex-col sm:flex-row group">
                  <div className="sm:w-5/12 aspect-[16/10] sm:aspect-auto overflow-hidden">
                    <img
                      src={formatImageUrl(post.featured_image)}
                      alt={post.title}
                      className="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                    />
                  </div>
                  <div className="p-4 sm:w-7/12 flex flex-col justify-between">
                    <div>
                      {post.category && (
                        <span 
                          className="inline-block px-2.5 py-0.5 rounded text-[11px] font-mono text-white mb-2"
                          style={{ backgroundColor: post.category.color || '#00ff66' }}
                        >
                          {post.category.name}
                        </span>
                      )}
                      <h4 className="font-cyber font-bold text-sm leading-snug group-hover:text-[#00ff66] transition-colors line-clamp-2">
                        <Link href={`/blog/${post.slug}`}>
                          {post.title}
                        </Link>
                      </h4>
                    </div>
                    <div className="flex items-center justify-between text-[11px] font-mono text-gray-400 pt-3 border-t border-[#00ff66]/10 mt-2">
                      <span>{new Date(post.published_at || post.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric' })}</span>
                      <span className="text-[#00ff66] flex items-center gap-1">
                        <i className="bi bi-clock"></i> {post.reading_time || 4}m
                      </span>
                    </div>
                  </div>
                </div>
              ))}
            </div>
          </div>
        </section>
      )}

      {/* 3. Trending Telemetry Section */}
      {trendingPosts.length > 0 && (
        <section className="border-y border-[#00ff66]/15 bg-[#091206]/35 py-10 my-6">
          <div className="max-w-7xl mx-auto px-4">
            <div className="flex items-center justify-between mb-6">
              <h3 className="font-cyber font-bold text-xl flex items-center gap-2">
                <i className="bi bi-cpu-fill text-[#00ff66] text-2xl"></i>
                <span>TRENDING // <span className="text-gradient">TELEMETRY</span></span>
              </h3>
              <Link href="/blog?sort=popular" className="btn-tactical text-xs px-3 py-1.5">
                DISPATCH LOG <i className="bi bi-arrow-right ml-1"></i>
              </Link>
            </div>

            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
              {trendingPosts.map((tPost: any, index: number) => (
                <div key={tPost.id} className="trending-card flex gap-3">
                  <span className="trend-number text-3xl font-bold">
                    0{index + 1}
                  </span>
                  <div className="flex flex-col justify-between">
                    <div>
                      <div className="flex items-center gap-2 mb-1">
                        <img 
                          src={formatImageUrl(tPost.author?.avatar)} 
                          alt={tPost.author?.name}
                          className="w-5 h-5 rounded-full object-cover"
                        />
                        <span className="text-xs font-semibold text-gray-300">{tPost.author?.name || 'Author'}</span>
                      </div>
                      <h5 className="font-bold text-xs text-white hover:text-[#00ff66] transition-colors line-clamp-2 leading-snug">
                        <Link href={`/blog/${tPost.slug}`}>
                          {tPost.title}
                        </Link>
                      </h5>
                    </div>
                    <div className="text-[11px] font-mono text-gray-400 flex items-center gap-2 mt-2">
                      <span>{new Date(tPost.published_at || tPost.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric' })}</span>
                      <span>•</span>
                      <span className="text-[#00ff66]">{tPost.views_count || 120} views</span>
                    </div>
                  </div>
                </div>
              ))}
            </div>
          </div>
        </section>
      )}

      {/* 4. Sectors / Topics Grid */}
      {categories.length > 0 && (
        <section className="max-w-7xl mx-auto px-4 py-8">
          <div className="flex items-center justify-between mb-6">
            <h3 className="font-cyber font-bold text-lg flex items-center gap-2">
              <i className="bi bi-grid-fill text-[#00ff66]"></i>
              <span>SECTORS // <span className="text-gradient-cyan">TOPICS</span></span>
            </h3>
            <Link href="/blog" className="btn-tactical text-xs px-3 py-1.5">
              ALL SECTORS <i className="bi bi-arrow-right ml-1"></i>
            </Link>
          </div>

          <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
            {categories.map((cat: any) => (
              <Link 
                key={cat.id} 
                href={`/category/${cat.slug}`}
                className="category-card p-4 flex flex-col items-center text-center group"
              >
                <div 
                  className="w-10 h-10 rounded-full flex items-center justify-center mb-2 transition-transform group-hover:scale-110"
                  style={{ backgroundColor: `${cat.color || '#00ff66'}25`, border: `1px solid ${cat.color || '#00ff66'}` }}
                >
                  <i className="bi bi-terminal text-sm" style={{ color: cat.color || '#00ff66' }}></i>
                </div>
                <h6 className="font-bold text-xs text-white group-hover:text-[#00ff66] transition-colors mb-1">
                  {cat.name}
                </h6>
                <span className="text-[10px] font-mono text-gray-400">
                  {cat.posts_count || 0} DOCS
                </span>
              </Link>
            ))}
          </div>
        </section>
      )}

      {/* 5. Datastream Latest Articles + Sticky Sidebar */}
      <section className="max-w-7xl mx-auto px-4 py-8">
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-8">
          {/* Main Articles Stream (8 cols) */}
          <div className="lg:col-span-8">
            <div className="flex items-center justify-between mb-6">
              <h3 className="font-cyber font-bold text-xl flex items-center gap-2">
                <i className="bi bi-lightning-charge-fill text-[#00ff66]"></i>
                <span>DATASTREAM // <span className="text-gradient">LATEST</span></span>
              </h3>
              <Link href="/blog" className="btn-tactical text-xs px-3 py-1.5">
                ALL DISPATCHES <i className="bi bi-arrow-right ml-1"></i>
              </Link>
            </div>

            <div className="grid grid-cols-1 sm:grid-cols-2 gap-6">
              {latestPosts.map((post: any) => (
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

            {/* Explore All Dispatches CTA */}
            <div className="text-center mt-10">
              <Link href="/blog" className="btn-gradient px-8 py-3 text-sm">
                EXPLORE ALL DISPATCHES <i className="bi bi-arrow-right ml-2"></i>
              </Link>
            </div>
          </div>

          {/* Sidebar (4 cols) */}
          <div className="lg:col-span-4">
            <Sidebar popularTags={popularTags} categories={categories} />
          </div>
        </div>
      </section>
    </div>
  );
}
