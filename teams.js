const players = JSON.parse(
    sessionStorage.getItem('floorball_selected_players') || '[]'
);
const teamSize = parseInt(
    sessionStorage.getItem('floorball_team_size'),
    10
);

const TEAM_COLORS = [
    'Orange',
    'Blue',
    'Yellow',
    'Black',
    'White'
];


if (!players.length || !teamSize) {
    document.getElementById('teams').innerHTML =
        '<p>No data available</p>';
    throw new Error('Missing data');
}

// 1️⃣ Score players
players.forEach(p => {
    p._score =
        (p.skill_level ?? 0) * 10 +
        (p.is_runner ? 5 : 0) +
        (p.gender === 'female' ? 2 : 0);
});

// 2️⃣ Shuffle players
players.sort(() => Math.random() - 0.5);

// 3️⃣ Create teams
const teamCount = Math.floor(players.length / teamSize);
const sparePlayers = players.length - (teamCount * teamSize);
const teams = Array.from({ length: teamCount }, () => ({
    players: [],
    score: 0
}));
maxPlayersPerTeam = teamSize + 1;


// 4️⃣ Distribute players

// Max size logic
const maxExtraTeams = sparePlayers;
const maxTeamSize = teamSize + 1;

players.forEach(player => {
    // Sort teams by current score (weakest first)
    teams.sort((a, b) => a.score - b.score);

    // Find first team that can still take a player
    const team = teams.find(t =>
        t.players.length < teamSize ||
        (
            t.players.length === teamSize &&
            teams.filter(x => x.players.length > teamSize).length < maxExtraTeams
        )
    );

    team.players.push(player);
    team.score += player._score;
});

// Give color to each team.
teams.forEach((team, index) => {
    team.color = TEAM_COLORS[index] ?? 'No color';
});

// 5️⃣ Render
const container = document.getElementById('teams');


teams.forEach((team, index) => {
    const colorStyle = {
        orange: '#f7931e',
        blue: '#007bff',
        yellow: '#f1c40f',
        black: '#333',
        white: '#eee'
    }[team.color.toLowerCase()] || '#ccc';

    const div = document.createElement('div');
    div.className = 'team';
    div.dataset.color = team.color;

    div.innerHTML = `
        <h3>
            <span style="
                display:inline-block;
                width:12px;
                height:12px;
                background:${colorStyle};
                border:1px solid #000;
                margin-right:6px;
                vertical-align:middle;
            "></span>
            Hold ${index + 1} – ${team.color}
        </h3>

        ${team.players.map(p => `
            <div>
                ${p.name}
            </div>
        `).join('')}
    `;

    container.appendChild(div);
});
