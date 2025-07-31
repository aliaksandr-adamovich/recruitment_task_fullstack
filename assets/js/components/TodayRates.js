import React, {useEffect, useState} from 'react';
import {useHistory} from 'react-router-dom';

export default function TodayRates() {
    const [rates, setRates] = useState([]);
    const history = useHistory();

    useEffect(() => {
        fetch('/api/rates/today')
            .then(res => res.json())
            .then(setRates)
            .catch(console.error);
    }, []);

    return (
        <div>
            <h2>Dzisiejsze kursy</h2>
            <table border="1" cellPadding="10">
                <thead>
                <tr>
                    <th>Waluta</th>
                    <th>Kurs średni</th>
                    <th>Kupno</th>
                    <th>Sprzedaż</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                {rates.map(rate => (
                    <tr key={rate.currency}>
                        <td>{rate.currency}</td>
                        <td>{rate.mid}</td>
                        <td>{rate.buy || '-'}</td>
                        <td>{rate.sell}</td>
                        <td>
                            <button onClick={() => history.push(`/history/${rate.currency}`)}>
                                Historia
                            </button>
                        </td>
                    </tr>
                ))}
                </tbody>
            </table>
        </div>
    );
}
