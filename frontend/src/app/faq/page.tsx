import React from 'react';
import Link from 'next/link';

export const metadata = {
  title: 'Frequently Asked Questions (FAQ) - Shrawan Effects',
  description: 'Everything you need to know about publishing, reading, and engaging on our platform.',
};

export default function FaqPage() {
  const faqs = [
    {
      q: 'How often is new content published?',
      a: 'We publish in-depth technical breakdowns, architectural tutorials, and AI automation insights on a regular weekly datastream.',
    },
    {
      q: 'What technologies are covered on Shrawan Effects?',
      a: 'Our core focus covers fullstack engineering: Laravel, Next.js, React, Node.js, PHP, DevOps/Docker, cloud architecture, and modern AI agent integrations.',
    },
    {
      q: 'Can I request custom web development or software consultancy?',
      a: 'Yes, our development studio in Faridabad delivers end-to-end custom web applications, API integrations, and cloud digital transformations.',
    },
    {
      q: 'How do I submit an article or become a contributing author?',
      a: 'You can navigate to the Author Portal (/login) and register your credentials. Authors can compose drafts in the portal for review.',
    },
  ];

  return (
    <div className="max-w-4xl mx-auto px-4 py-12">
      <div className="text-xs font-mono text-gray-400 mb-4 flex items-center gap-2">
        <Link href="/" className="hover:text-[#00ff66]">Home</Link>
        <span>/</span>
        <span className="text-[#00ff66]">FAQ</span>
      </div>

      <div className="mb-10 text-center">
        <h1 className="font-cyber font-bold text-3xl sm:text-4xl text-white mb-3">
          Frequently Asked <span className="text-gradient">Questions</span>
        </h1>
        <p className="text-gray-400 text-sm max-w-lg mx-auto">
          Everything you need to know about our technology datastream and agency capabilities.
        </p>
      </div>

      <div className="space-y-4">
        {faqs.map((faq, idx) => (
          <div key={idx} className="bg-[#091206] border border-[#00ff66]/25 rounded-2xl p-6 shadow-lg">
            <h3 className="font-cyber font-bold text-base text-white mb-2 flex items-center gap-2">
              <span className="text-[#00ff66] font-mono text-sm">Q{idx + 1}.</span> {faq.q}
            </h3>
            <p className="text-gray-300 text-sm leading-relaxed pl-6">
              {faq.a}
            </p>
          </div>
        ))}
      </div>
    </div>
  );
}
