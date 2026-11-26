import './globals.css';
import Header from '@/components/Header';
import Footer from '@/components/Footer';
import { fetchApi } from '@/lib/api';
import type { Metadata } from 'next';

export async function generateMetadata(): Promise<Metadata> {
  const settings = await fetchApi('/settings');
  return {
    title: settings?.site_name ? `${settings.site_name} | Fullstack Web Development & AI Automation` : 'Shrawan Effects',
    description: settings?.site_description || 'Explore cutting-edge fullstack web development, Laravel & React engineering, and AI automation solutions at Shrawan Effects.',
    icons: {
      icon: settings?.site_favicon || 'https://shrawaneffects.com/storage/settings/nS0WZEtEZ8c1ivBACBfE2uHexzYBllzdrzIQS8MV.png',
    },
  };
}

export default async function RootLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  const settings = await fetchApi('/settings');

  return (
    <html lang="en" data-bs-theme="dark">
      <head>
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossOrigin="anonymous" />
        <link rel="preconnect" href="https://cdn.jsdelivr.net" crossOrigin="anonymous" />
      </head>
      <body className="antialiased min-h-screen flex flex-col">
        <Header settings={settings} />
        <main className="flex-grow">
          {children}
        </main>
        <Footer settings={settings} />
      </body>
    </html>
  );
}
