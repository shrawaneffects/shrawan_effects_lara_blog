@extends('frontend.openseo.layouts.openseo')

@section('title', 'AI Visibility & LLM Brand Tracking - ' . $activeProject->domain . ' - OpenSEO')

@section('openseo_content')
<!-- KPI Row -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-4">
        <div class="openseo-hud-card p-3 p-md-4 h-100">
            <span class="text-body-secondary font-mono small text-uppercase d-block mb-1">AI Prompts Evaluated</span>
            <div class="openseo-stat-num">{{ $totalChecked }}</div>
            <span class="text-body-secondary font-mono small mt-2 d-block">Search prompts tested across LLM models</span>
        </div>
    </div>
    <div class="col-sm-6 col-xl-4">
        <div class="openseo-hud-card p-3 p-md-4 h-100">
            <span class="text-body-secondary font-mono small text-uppercase d-block mb-1">Brand Mentions & Citations</span>
            <div class="openseo-stat-num text-success">{{ $citedCount }}</div>
            <span class="text-success font-mono small mt-2 d-block">
                <i class="bi bi-robot me-1"></i> {{ round(($citedCount / max(1, $totalChecked)) * 100) }}% Inclusion Rate
            </span>
        </div>
    </div>
    <div class="col-sm-6 col-xl-4">
        <div class="openseo-hud-card p-3 p-md-4 h-100">
            <span class="text-body-secondary font-mono small text-uppercase d-block mb-1">Sentiment Rating</span>
            <div class="openseo-stat-num text-info">
                {{ round(($positiveCount / max(1, $totalChecked)) * 100) }}%
            </div>
            <span class="text-info font-mono small mt-2 d-block">
                <i class="bi bi-emoji-smile-fill me-1"></i> Positive Brand Authority
            </span>
        </div>
    </div>
</div>

<!-- Test AI Brand Presence Form -->
<div class="openseo-hud-card p-3 p-md-4 mb-4">
    <h5 class="font-cyber fw-bold text-white mb-1">Audit Brand Visibility in AI Search</h5>
    <p class="text-body-secondary font-mono small mb-3">Check whether ChatGPT, Claude, Gemini, or Perplexity recommend {{ $activeProject->name }} for industry queries</p>

    <form action="{{ route('openseo.ai-visibility.check') }}" method="POST" class="row g-2 align-items-center">
        @csrf
        <div class="col-md-7">
            <input type="text" name="prompt_query" class="form-control form-control-lg bg-dark text-white border-secondary border-opacity-50 font-mono" placeholder="Ask AI (e.g. Best software developer in Faridabad, Top web agency)..." required>
        </div>
        <div class="col-md-3">
            <select name="ai_model" class="form-select form-select-lg bg-dark text-white border-secondary border-opacity-50 font-mono">
                <option value="ChatGPT">ChatGPT (OpenAI Search)</option>
                <option value="Gemini">Google Gemini</option>
                <option value="Perplexity">Perplexity AI</option>
                <option value="Claude">Claude 3.7 Sonnet</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-lg btn-gradient w-100 font-mono">
                <i class="bi bi-cpu me-1"></i> Run Audit
            </button>
        </div>
    </form>
</div>

<!-- AI Citations & Answers Table -->
<div class="openseo-hud-card p-3 p-md-4">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h5 class="font-cyber fw-bold text-white mb-0">AI Mentions & Response Snippets</h5>
            <p class="text-body-secondary font-mono small mb-0">Real model responses citing {{ $activeProject->name }}</p>
        </div>
        <span class="badge bg-dark text-info border border-info border-opacity-50 font-mono">
            <span class="cyber-beacon me-1"></span>LLM CITATION ACTIVE
        </span>
    </div>

    <div class="table-responsive">
        <table class="table openseo-table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>AI Model</th>
                    <th>Prompt Query</th>
                    <th class="text-center">Mentioned</th>
                    <th class="text-center">Rank</th>
                    <th>Sentiment</th>
                    <th>AI Response / Citation Snippet</th>
                    <th class="text-end">Checked</th>
                </tr>
            </thead>
            <tbody>
                @forelse($mentions as $m)
                    <tr>
                        <td>
                            @php
                                $badgeStyle = match($m->ai_model) {
                                    'ChatGPT' => 'bg-success bg-opacity-25 text-success border-success',
                                    'Gemini' => 'bg-primary bg-opacity-25 text-primary border-primary',
                                    'Perplexity' => 'bg-info bg-opacity-25 text-info border-info',
                                    'Claude' => 'bg-warning bg-opacity-25 text-warning border-warning',
                                    default => 'bg-dark text-white border-secondary',
                                };
                            @endphp
                            <span class="badge {{ $badgeStyle }} border border-opacity-50 font-mono fs-6 px-2.5 py-1.5">
                                {{ $m->ai_model }}
                            </span>
                        </td>
                        <td>
                            <span class="text-white fw-bold font-mono small">"{{ $m->prompt_query }}"</span>
                        </td>
                        <td class="text-center">
                            @if($m->is_mentioned)
                                <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-25 font-mono">
                                    <i class="bi bi-check-circle-fill me-1"></i> Cited
                                </span>
                            @else
                                <span class="badge bg-secondary bg-opacity-25 text-body-secondary font-mono">Not Cited</span>
                            @endif
                        </td>
                        <td class="text-center font-mono">
                            <span class="badge bg-dark text-success border border-success border-opacity-50 px-2.5 py-1">
                                #{{ $m->mention_rank ?? 1 }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-dark text-info border border-info border-opacity-25 font-mono">
                                {{ $m->sentiment }}
                            </span>
                        </td>
                        <td>
                            <div class="text-slate-300 small p-2 rounded bg-dark border border-secondary border-opacity-25 font-mono" style="max-width: 480px; font-size: 0.78rem;">
                                {{ $m->response_snippet }}
                            </div>
                        </td>
                        <td class="text-end font-mono small text-body-secondary text-nowrap">
                            {{ $m->last_checked_at ? $m->last_checked_at->diffForHumans() : 'Just now' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-body-secondary font-mono py-4">
                            No AI checks performed yet. Use the prompt query box above to test your brand visibility in AI engines.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($mentions->hasPages())
        <div class="p-3 border-top border-secondary border-opacity-25">
            {{ $mentions->links() }}
        </div>
    @endif
</div>
@endsection
