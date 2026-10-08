// Two components, nine islands: TeamStats once, MemberCard once per member. Each island is
// its own React root with its own state - the kudos of one card are not the other's.

function TeamStats({ people, commits, cities, teams }) {
  const tiles = [
    ['People', people], ['Commits', commits], ['Teams', teams], ['Cities', cities]
  ];
  return (
    <div className="grid" style={{ gridTemplateColumns: 'repeat(auto-fit, minmax(150px, 1fr))' }}>
      {tiles.map(([label, value]) => (
        <div key={label} className="panel">
          <div className="panel-title">{label}</div>
          <div style={{ fontSize: 30, fontWeight: 800 }}><CountUp value={value} /></div>
        </div>
      ))}
    </div>
  );
}

function MemberCard({ name, role, team, city, joined, commits, most, hue, skills }) {
  const [kudos, setKudos] = React.useState(0);
  const [open, setOpen] = React.useState(false);
  const years = Math.max(0, new Date().getFullYear() - new Date(joined).getFullYear());

  return (
    <div className="card" style={{ height: '100%' }}>
      <div style={{ display: 'flex', gap: 12, alignItems: 'center' }}>
        <Avatar name={name} hue={hue} />
        <div style={{ minWidth: 0 }}>
          <div style={{ fontWeight: 800 }}>{name}</div>
          <div className="muted small">{role} &middot; {city}</div>
        </div>
      </div>

      <div className="chips" style={{ margin: '14px 0' }}>
        <span className="badge is-pad">{team}</span>
        {skills.map(skill => <span key={skill} className="badge">{skill}</span>)}
      </div>

      <div className="muted small" style={{ display: 'flex', justifyContent: 'space-between' }}>
        <span>commits</span><span className="num">{commits.toLocaleString('en')}</span>
      </div>
      <div className="bar" style={{ margin: '4px 0 14px' }}><span style={{ width: `${commits / most * 100}%` }} /></div>

      <div style={{ display: 'flex', gap: 8, alignItems: 'center' }}>
        <button className="btn-sm" onClick={() => setKudos(kudos + 1)}>
          👏 <span key={kudos} className={kudos ? 'pop' : ''}>{kudos}</span>
        </button>
        <button className="btn-sm btn-ghost" onClick={() => setOpen(!open)}>{open ? 'Less' : 'More'}</button>
      </div>

      {open && (
        <div className="panel rise" style={{ marginTop: 12, fontSize: 14 }}>
          Joined {joined}{years > 0 ? `, ${years} year${years === 1 ? '' : 's'} ago` : ''}. These props came from
          <code> _data/team.json</code>, through PHP, into <code>data-props</code>.
        </div>
      )}
    </div>
  );
}

PadReact.island('TeamStats', TeamStats);
PadReact.island('MemberCard', MemberCard);
