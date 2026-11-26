'use client';

import React, { useState, useEffect } from 'react';
import Link from 'next/link';
import { useRouter } from 'next/navigation';

export default function Header({ settings }: { settings?: any }) {
  const [theme, setTheme] = useState<'dark' | 'light'>('dark');
  const [search, setSearch] = useState('');
  const [servicesOpen, setServicesOpen] = useState(false);
  const router = useRouter();

  useEffect(() => {
    const savedTheme = localStorage.getItem('se_theme') as 'dark' | 'light' || 'dark';
    setTheme(savedTheme);
    document.documentElement.setAttribute('data-bs-theme', savedTheme);
  }, []);

  const toggleTheme = () => {
    const nextTheme = theme === 'dark' ? 'light' : 'dark';
    setTheme(nextTheme);
    localStorage.setItem('se_theme', nextTheme);
    document.documentElement.setAttribute('data-bs-theme', nextTheme);
  };

  const handleSearch = (e: React.FormEvent) => {
    e.preventDefault();
    if (search.trim()) {
      router.push(`/blog?search=${encodeURIComponent(search.trim())}`);
    }
  };

  const logoUrl = settings?.site_logo || 'https://shrawaneffects.com/storage/settings/FmPxHvRuuMFNwblYELBqVPuJIK61rsCHrWlSSSTE.png';

  return (
    <header className="sticky top-0 z-50">
      {/* 3px Top Laser Scan Bar */}
      <div className="top-gradient-bar" />

      {/* Glass Navbar */}
      <nav className="navbar-glass px-4 py-3">
        <div className="max-w-7xl mx-auto flex items-center justify-between gap-4">
          
          {/* Logo & Main Nav */}
          <div className="flex items-center gap-8">
            <Link href="/" className="flex items-center gap-2">
              <img 
                src={logoUrl} 
                alt="Shrawan Effects" 
                className="h-9 w-auto object-contain"
                onError={(e) => {
                  (e.target as HTMLElement).style.display = 'none';
                }}
              />
              <span className="font-cyber font-bold text-xl tracking-wider text-white">
                SHRAWAN <span className="text-[#00ff66]">EFFECTS</span>
              </span>
            </Link>

            <div className="hidden md:flex items-center gap-6 font-cyber text-sm tracking-wide">
              <Link href="/" className="hover:text-[#00ff66] transition-colors flex items-center gap-1.5">
                <i className="bi bi-house"></i> Home
              </Link>

              {/* Services Dropdown */}
              <div className="relative">
                <button 
                  onClick={() => setServicesOpen(!servicesOpen)}
                  className="hover:text-[#00ff66] transition-colors flex items-center gap-1.5"
                >
                  <i className="bi bi-grid"></i> Services <i className="bi bi-chevron-down text-xs"></i>
                </button>
                {servicesOpen && (
                  <div 
                    className="absolute top-full left-0 mt-2 w-48 bg-[#091206] border border-[#00ff66]/30 rounded-xl shadow-xl py-2 z-50 backdrop-blur-lg"
                    onMouseLeave={() => setServicesOpen(false)}
                  >
                    <Link 
                      href="/blog" 
                      className="block px-4 py-2 text-sm hover:bg-[#213608]/50 hover:text-[#00ff66] transition-colors"
                      onClick={() => setServicesOpen(false)}
                    >
                      Web Development
                    </Link>
                    <Link 
                      href="/blog" 
                      className="block px-4 py-2 text-sm hover:bg-[#213608]/50 hover:text-[#00ff66] transition-colors"
                      onClick={() => setServicesOpen(false)}
                    >
                      AI Automation
                    </Link>
                  </div>
                )}
              </div>

              <Link href="/blog" className="hover:text-[#00ff66] transition-colors flex items-center gap-1.5">
                <i className="bi bi-journal-text"></i> Articles
              </Link>

              <Link href="/faq" className="hover:text-[#00ff66] transition-colors flex items-center gap-1.5">
                <i className="bi bi-question-circle"></i> FAQ
              </Link>

              <Link href="/contact" className="hover:text-[#00ff66] transition-colors flex items-center gap-1.5">
                <i className="bi bi-envelope"></i> Contact Us
              </Link>
            </div>
          </div>

          {/* Right Tools (Search, Theme Toggle, Login/Register) */}
          <div className="flex items-center gap-3">
            {/* Search Input */}
            <form onSubmit={handleSearch} className="hidden lg:flex items-center relative">
              <input
                type="text"
                placeholder="Search telemetry..."
                value={search}
                onChange={(e) => setSearch(e.target.value)}
                className="bg-[#091206]/80 border border-[#00ff66]/30 rounded-full px-4 py-1.5 pl-9 text-xs font-mono text-white placeholder-gray-400 focus:outline-none focus:border-[#00ff66] w-48 transition-all focus:w-64"
              />
              <i className="bi bi-search absolute left-3 text-xs text-[#00ff66]"></i>
            </form>

            {/* Theme Toggle */}
            <button 
              onClick={toggleTheme}
              className="btn-theme-toggle"
              title="Toggle theme"
            >
              <i className={`bi ${theme === 'dark' ? 'bi-sun-fill text-warning' : 'bi-moon-stars-fill text-[#00ff66]'}`}></i>
            </button>

            {/* Portal Login */}
            <a 
              href="http://127.0.0.1:8000/login" 
              className="btn-tactical text-xs"
            >
              LOGIN
            </a>

            {/* Register */}
            <a 
              href="http://127.0.0.1:8000/register" 
              className="btn-gradient text-xs"
            >
              REGISTER
            </a>
          </div>

        </div>
      </nav>
    </header>
  );
}
