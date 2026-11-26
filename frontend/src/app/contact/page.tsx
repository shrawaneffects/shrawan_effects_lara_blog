'use client';

import React, { useState } from 'react';
import Link from 'next/link';

export default function ContactPage() {
  const [formData, setFormData] = useState({ name: '', email: '', subject: '', message: '' });
  const [status, setStatus] = useState<string | null>(null);
  const [submitting, setSubmitting] = useState(false);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setSubmitting(true);
    setStatus(null);
    try {
      const res = await fetch('/api/contact', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(formData),
      });
      const data = await res.json();
      if (res.ok) {
        setStatus(data.message || 'Your transmission has been logged.');
        setFormData({ name: '', email: '', subject: '', message: '' });
      } else {
        setStatus('Error logging transmission. Please check your inputs.');
      }
    } catch {
      setStatus('Network connection error.');
    } finally {
      setSubmitting(false);
    }
  };

  return (
    <div className="max-w-5xl mx-auto px-4 py-12">
      <div className="text-xs font-mono text-gray-400 mb-4 flex items-center gap-2">
        <Link href="/" className="hover:text-[#00ff66]">Home</Link>
        <span>/</span>
        <span className="text-[#00ff66]">Contact</span>
      </div>

      <div className="mb-12">
        <div className="telemetry-tag mb-3">
          <i className="bi bi-geo-alt-fill"></i> FARIDABAD, HARYANA // STUDIO HQ
        </div>
        <h1 className="font-cyber font-bold text-3xl sm:text-4xl text-white mb-3">
          Contact <span className="text-gradient">Shrawan Effects</span>
        </h1>
        <p className="text-gray-400 text-sm max-w-xl">
          Fullstack Website & Software Development Service Provider in Faridabad. Let's discuss your project blueprints, digital transformation, or custom cloud applications.
        </p>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-12 gap-8">
        {/* Info Column (5 cols) */}
        <div className="md:col-span-5 space-y-6">
          <div className="bg-[#091206] border border-[#00ff66]/25 rounded-2xl p-6 shadow-xl">
            <h4 className="font-cyber font-bold text-base text-white mb-4">
              Development Studio & Office
            </h4>
            <div className="space-y-4 text-xs font-mono text-gray-300">
              <div className="flex items-start gap-3">
                <i className="bi bi-geo-alt-fill text-[#00ff66] text-base mt-0.5"></i>
                <div>
                  <div className="font-bold text-white mb-0.5">Headquarters</div>
                  <p className="text-gray-400 leading-relaxed">
                    House No. 1049, Jeevan Nagar, Gounchhi, FARIDABAD, Haryana 121004
                  </p>
                </div>
              </div>

              <div className="flex items-start gap-3">
                <i className="bi bi-envelope-fill text-[#00ff66] text-base mt-0.5"></i>
                <div>
                  <div className="font-bold text-white mb-0.5">Direct Dispatch</div>
                  <a href="mailto:info@shrawaneffects.com" className="text-[#00ff66] hover:underline">
                    info@shrawaneffects.com
                  </a>
                </div>
              </div>

              <div className="flex items-start gap-3">
                <i className="bi bi-clock-fill text-[#00ff66] text-base mt-0.5"></i>
                <div>
                  <div className="font-bold text-white mb-0.5">Operating Hours</div>
                  <p className="text-gray-400">Mon - Sat: 09:00 - 19:00 IST</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        {/* Form Column (7 cols) */}
        <div className="md:col-span-7">
          <div className="bg-[#091206] border border-[#00ff66]/25 rounded-2xl p-6 shadow-xl">
            <h4 className="font-cyber font-bold text-base text-white mb-4">
              Initiate Direct Transmission
            </h4>

            {status && (
              <div className="mb-4 p-3 rounded-xl bg-[#213608]/70 border border-[#00ff66]/40 text-xs text-[#00ff66] font-mono">
                {status}
              </div>
            )}

            <form onSubmit={handleSubmit} className="space-y-4">
              <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label className="block text-xs font-mono text-gray-400 mb-1">Your Name *</label>
                  <input
                    type="text"
                    required
                    value={formData.name}
                    onChange={(e) => setFormData({ ...formData, name: e.target.value })}
                    className="w-full bg-[#050804] border border-[#00ff66]/30 rounded-xl px-3 py-2 text-xs font-mono text-white placeholder-gray-500 focus:outline-none focus:border-[#00ff66]"
                  />
                </div>
                <div>
                  <label className="block text-xs font-mono text-gray-400 mb-1">Email Address *</label>
                  <input
                    type="email"
                    required
                    value={formData.email}
                    onChange={(e) => setFormData({ ...formData, email: e.target.value })}
                    className="w-full bg-[#050804] border border-[#00ff66]/30 rounded-xl px-3 py-2 text-xs font-mono text-white placeholder-gray-500 focus:outline-none focus:border-[#00ff66]"
                  />
                </div>
              </div>

              <div>
                <label className="block text-xs font-mono text-gray-400 mb-1">Subject</label>
                <input
                  type="text"
                  value={formData.subject}
                  onChange={(e) => setFormData({ ...formData, subject: e.target.value })}
                  placeholder="Project inquiry or consultation..."
                  className="w-full bg-[#050804] border border-[#00ff66]/30 rounded-xl px-3 py-2 text-xs font-mono text-white placeholder-gray-500 focus:outline-none focus:border-[#00ff66]"
                />
              </div>

              <div>
                <label className="block text-xs font-mono text-gray-400 mb-1">Message Description *</label>
                <textarea
                  required
                  rows={5}
                  value={formData.message}
                  onChange={(e) => setFormData({ ...formData, message: e.target.value })}
                  placeholder="Detailed project requirements, architecture specifications..."
                  className="w-full bg-[#050804] border border-[#00ff66]/30 rounded-xl px-3 py-2 text-xs font-mono text-white placeholder-gray-500 focus:outline-none focus:border-[#00ff66]"
                />
              </div>

              <button
                type="submit"
                disabled={submitting}
                className="btn-gradient w-full py-2.5 text-xs font-bold"
              >
                {submitting ? 'TRANSMITTING...' : 'DISPATCH TRANSMISSION'}
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  );
}
