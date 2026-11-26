'use client';

import React, { useState } from 'react';
import Link from 'next/link';

export default function Footer({ settings }: { settings?: any }) {
  const [email, setEmail] = useState('');
  const [status, setStatus] = useState<string | null>(null);

  const handleSubscribe = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!email) return;
    try {
      const res = await fetch('/api/newsletter', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email }),
      });
      const data = await res.json();
      setStatus(data.message || 'Transmission recorded!');
      setEmail('');
    } catch {
      setStatus('Telemetry server connection error');
    }
  };

  const logoUrl = settings?.site_logo || 'https://shrawaneffects.com/storage/settings/FmPxHvRuuMFNwblYELBqVPuJIK61rsCHrWlSSSTE.png';

  return (
    <footer className="mt-auto border-t border-[#00ff66]/20 bg-[#050804] py-12 px-4">
      <div className="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-12 gap-8 mb-12">
        
        {/* Col 1: Brand & HQ */}
        <div className="md:col-span-5 flex flex-col">
          <Link href="/" className="mb-4 inline-block">
            <img 
              src={logoUrl} 
              alt="Shrawan Effects" 
              className="h-9 w-auto object-contain"
            />
          </Link>
          <p className="text-gray-400 text-sm mb-4 leading-relaxed max-w-sm">
            {settings?.footer_text || 'Shrawan Effects is an agency where you can ask anything and that will make your business grow and profitable.'}
          </p>
          <div className="text-gray-400 text-xs font-mono space-y-1 mb-4">
            <div className="flex items-start gap-2">
              <i className="bi bi-geo-alt-fill text-[#00ff66] mt-0.5"></i>
              <span>{settings?.contact_address || 'House No. 1049, Jeevan Nagar, Gounchhi, FARIDABAD, Haryana 121004'}</span>
            </div>
          </div>
          <div className="flex items-center gap-4 text-lg text-gray-400">
            <a href={settings?.github_url || 'https://github.com/shrawaneffects'} target="_blank" rel="noopener noreferrer" className="hover:text-[#00ff66] transition-colors">
              <i className="bi bi-github"></i>
            </a>
            <a href={settings?.twitter_url || 'https://twitter.com/shrawaneffects'} target="_blank" rel="noopener noreferrer" className="hover:text-[#00ff66] transition-colors">
              <i className="bi bi-twitter-x"></i>
            </a>
            <a href={settings?.linkedin_url || 'https://www.linkedin.com/company/shrawan-effects/'} target="_blank" rel="noopener noreferrer" className="hover:text-[#00ff66] transition-colors">
              <i className="bi bi-linkedin"></i>
            </a>
          </div>
        </div>

        {/* Col 2: Direct Protocols */}
        <div className="md:col-span-3">
          <h6 className="font-cyber font-bold text-xs uppercase tracking-wider text-[#00ff66] mb-4">
            DIRECT PROTOCOLS
          </h6>
          <div className="grid grid-cols-2 gap-2 text-sm text-gray-300">
            <div className="space-y-2">
              <div><Link href="/" className="hover:text-[#00ff66] transition-colors">Home</Link></div>
              <div><Link href="/blog" className="hover:text-[#00ff66] transition-colors">All Articles</Link></div>
              <div><Link href="/faq" className="hover:text-[#00ff66] transition-colors">FAQ</Link></div>
              <div><Link href="/contact" className="hover:text-[#00ff66] transition-colors">Contact Us</Link></div>
              <div><a href="http://127.0.0.1:8000/login" className="hover:text-[#00ff66] transition-colors">Author Portal</a></div>
            </div>
            <div className="space-y-2">
              <div><Link href="/category/laravel-php" className="hover:text-[#00ff66] transition-colors">Laravel & PHP</Link></div>
              <div><Link href="/category/web-development" className="hover:text-[#00ff66] transition-colors">Web Dev</Link></div>
              <div><Link href="/category/ui-ux-design" className="hover:text-[#00ff66] transition-colors">UI/UX Design</Link></div>
              <div><Link href="/category/ai-machine-learning" className="hover:text-[#00ff66] transition-colors">AI & Agents</Link></div>
            </div>
          </div>
        </div>

        {/* Col 3: Cipher Transmission */}
        <div className="md:col-span-4">
          <h6 className="font-cyber font-bold text-xs uppercase tracking-wider text-[#00ff66] mb-4">
            CIPHER TRANSMISSION
          </h6>
          <p className="text-gray-400 text-xs mb-3">
            Subscribe to receive weekly telemetry curations on architecture, Laravel innovations, and futuristic tech.
          </p>
          <form onSubmit={handleSubscribe} className="space-y-2">
            <div className="flex gap-2">
              <input
                type="email"
                placeholder="user@domain.com"
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                required
                className="bg-[#091206] border border-[#00ff66]/30 rounded-lg px-3 py-2 text-xs font-mono text-white placeholder-gray-500 focus:outline-none focus:border-[#00ff66] flex-grow"
              />
              <button type="submit" className="btn-gradient text-xs px-4 py-2">
                TRANSMIT
              </button>
            </div>
            {status && (
              <p className="text-xs text-[#00ff66] font-mono mt-1">{status}</p>
            )}
          </form>
        </div>

      </div>

      {/* Bottom Bar */}
      <div className="max-w-7xl mx-auto pt-6 border-t border-[#00ff66]/10 flex flex-col sm:flex-row items-center justify-between text-xs text-gray-400 font-mono gap-2">
        <div>
          © 2026 SHRAWAN EFFECTS // ALL RIGHTS RESERVED.
        </div>
        <div className="flex items-center gap-1.5 text-[#00ff66]">
          <i className="bi bi-shield-check"></i>
          <span>Everything is https</span>
        </div>
      </div>
    </footer>
  );
}
