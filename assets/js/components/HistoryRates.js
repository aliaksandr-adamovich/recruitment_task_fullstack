import React, {useEffect, useState} from 'react';
import {useParams, useHistory} from 'react-router-dom';

export default function HistoryRates() {
    const {currency} = useParams();
    const history = useHistory();
    const [rates, setRates] = useState([]);
    const [date, setDate] = useState(new Date().toISOString().substring(0, 10));
    const [error, setError] = useState(null);

    useEffect(() => {
        fetch(`/api/rates/history/${currency}/${date}`)
            .then(res => {
                if (!res.ok) {
                    throw new Error(`HTTP ${res.status} - ${res.statusText}`);
                }
                return res.json();
            })
            .then(data => {
                setRates(data);
            })
            .catch(err => {
                setRates([]);
                console.error(err);
            });
    }, [currency, date]);

    return (
        <div>
            <h2>Historia kursu: {currency}</h2>
            <button onClick={() => history.push('/rates')}>← Powrót</button>

            <div style={{marginTop: '1rem'}}>
                <label>Wybierz datę: </label>
                <input
                    type="date"
                    value={date}
                    onChange={e => setDate(e.target.value)}
                    max={new Date().toISOString().substring(0, 10)}
                />
            </div>

            {error && <div style={{color: 'red', marginTop: '1rem'}}>{error}</div>}

            {!error && (
                <table border="1" cellPadding="10" style={{marginTop: '1rem'}}>
                    <thead>
                    <tr>
                        <th>Data</th>
                        <th>Kurs średni</th>
                        <th>Kupno</th>
                        <th>Sprzedaż</th>
                    </tr>
                    </thead>
                    <tbody>
                    {rates.map(rate => (
                        <tr key={rate.date}>
                            <td>{rate.date}</td>
                            <td>{rate.mid}</td>
                            <td>{rate.buy || '-'}</td>
                            <td>{rate.sell}</td>
                        </tr>
                    ))}
                    </tbody>
                </table>
            )}
        </div>
    );
}
