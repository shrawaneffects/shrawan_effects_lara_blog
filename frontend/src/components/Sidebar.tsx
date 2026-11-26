'use client';

import React, { useState } from 'react';
import Link from 'next/link';
import { useRouter } from 'next/navigation';

export default function Sidebar({ 
  popularTags = [], 
  categories = [] 
}: { 
  popularTags?: any[]; 
  categories?: any[];
}) {
  const [search, setSearch] = useState('');
  const [newsletterEmail, setNewsletterEmail] = useState('');
  const [newsletterStatus, setNewsletterStatus] = useState<string | null>(null);
  const router = useRouter();

  const handleSearch = (e: React.FormEvent) => {
    e.preventDefault();
    if (search.trim()) {
      router.push(`/blog?search=${encodeURIComponent(search.trim())}`);
    }
  };

  const handleNewsletter = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!newsletterEmail) return;
    try {
      const res = await fetch('/api/newsletter', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email: newsletterEmail }),
      });
      const data = await res.json();
      setNewsletterStatus(data.message || 'Subscription logged!');
      setNewsletterEmail('');
    } catch {
      setNewsletterStatus('Connection disrupted');
    }
  };

  return (
    <aside className="space-y-6 sticky top-24">
      {/* 1. Database Query (Search) */}
      <div className="bg-[#091206]/85 border border-[#00ff66]/20 rounded-2xl p-5 shadow-lg">
        <h5 className="font-cyber font-bold text-sm tracking-wider text-white mb-3 flex items-center gap-2">
          <i className="bi bi-terminal text-[#00ff66]"></i> DATABASE QUERY
        </h5>
        <form onSubmit={handleSearch} className="flex gap-2">
          <input
            type="text"
            placeholder="Search keywords..."
            value={search}
            onChange={(e) => setSearch(e.target.value)}
            className="bg-[#050804] border border-[#00ff66]/30 rounded-xl px-3 py-2 text-xs font-mono text-white placeholder-gray-500 focus:outline-none focus:border-[#00ff66] flex-grow"
          />
          <button type="submit" className="btn-gradient px-3 py-2 text-xs">
            <i className="bi bi-search"></i>
          </button>
        </form>
      </div>

      {/* 2. Categories Accordion / List */}
      {categories.length > 0 && (
        <div className="bg-[#091206]/85 border border-[#00ff66]/20 rounded-2xl p-5 shadow-lg">
          <h5 className="font-cyber font-bold text-sm tracking-wider text-white mb-3 flex items-center gap-2">
            <i className="bi bi-folder2-open text-[#00ff66]"></i> SECTORS // TOPICS
          </h5>
          <div className="space-y-2">
            {categories.map((cat: any) => (
              <Link
                key={cat.id}
                href={`/category/${cat.slug}`}
                className="flex items-center justify-between py-1.5 px-2 rounded-lg hover:bg-[#213608]/40 transition-colors text-xs text-gray-300 hover:text-white"
              >
                <span className="flex items-center gap-2">
                  <span 
                    className="w-2 h-2 rounded-full inline-block" 
                    style={{ backgroundColor: cat.color || '#00ff66' }}
                  />
                  {cat.name}
                </span>
                <span className="font-mono text-[11px] text-[#00ff66] bg-[#213608]/60 px-2 py-0.5 rounded-full border border-[#00ff66]/20">
                  {cat.posts_count || 0}
                </span>
              </Link>
            ))}
          </div>
        </div>
      )}

      {/* 3. Intel Tags Cloud */}
      {popularTags.length > 0 && (
        <div className="bg-[#091206]/85 border border-[#00ff66]/20 rounded-2xl p-5 shadow-lg">
          <h5 className="font-cyber font-bold text-sm tracking-wider text-white mb-3 flex items-center gap-2">
            <i className="bi bi-tags text-[#00ff66]"></i> INTEL TAGS
          </h5>
          <div className="flex flex-wrap gap-2">
            {popularTags.map((tag: any) => (
              <Link
                key={tag.id}
                href={`/tag/${tag.slug}`}
                className="font-mono text-[11px] px-2.5 py-1 rounded-lg bg-[#213608]/40 hover:bg-[#00ff66] hover:text-[#050804] text-gray-300 border border-[#00ff66]/25 transition-all flex items-center gap-1"
              >
                #{tag.name}
                {tag.posts_count ? <span className="opacity-70 text-[10px]">({tag.posts_count})</span> : null}
              </Link>
            ))}
          </div>
        </div>
      )}

      {/* 4. Cipher Transmission Box */}
      <div className="card-newsletter-gradient p-5 shadow-xl">
        <div className="flex items-center gap-2 mb-2 text-[#00ff66]">
          <i className="bi bi-broadcast fs-5"></i>
          <span className="font-cyber font-bold text-sm text-white">CIPHER TRANSMISSION</span>
        </div>
        <p className="text-gray-300 text-xs mb-4">
          Weekly telemetry intelligence on fullstack architectures & AI solutions.
        </p>
        <form onSubmit={handleNewsletter} className="space-y-2">
          <input
            type="email"
            placeholder="user@domain.com"
            value={newsletterEmail}
            onChange={(e) => setNewsletterEmail(e.target.value)}
            required
            className="w-full bg-[#050804] border border-[#00ff66]/30 rounded-xl px-3 py-2 text-xs font-mono text-white placeholder-gray-500 focus:outline-none focus:border-[#00ff66]"
          />
          <button type="submit" className="w-full btn-gradient text-xs py-2">
            INITIATE SUBSCRIPTION
          </button>
          {newsletterStatus && (
            <p className="text-[11px] text-[#00ff66] font-mono mt-1 text-center">{newsletterStatus}</p>
          )}
        </form>
      </div>
    </aside>
  );
}
