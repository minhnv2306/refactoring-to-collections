<?php

if (! function_exists('assign_initial_rankings')) {
    function assign_initial_rankings($scores)
    {
        return $scores->sortByDesc('score')
            ->zip(range(1, $scores->count()))
            ->map(function ($scoreAndRank) {
                list($score, $rank) = $scoreAndRank;
                return array_merge($score, [
                    'rank' => $rank
                ]);
            });
    }
}

if (! function_exists('adjust_rankings_for_ties')) {
    function adjust_rankings_for_ties($scores)
    {
        return $scores->groupBy('score')->map(function ($tiedScores) {
            return apply_min_rank($tiedScores);
        })->collapse();
    }
}

if (! function_exists('apply_min_rank')) {
    function apply_min_rank($tiedScores)
    {
        $lowestRank = $tiedScores->pluck('rank')->min();
        return $tiedScores->map(function ($rankedScore) use ($lowestRank) {
            return array_merge($rankedScore, [
                'rank' => $lowestRank
            ]);
        });
    }
}
