// ./assets/js/components/Home.js

import React, {Component} from 'react';
import {Route, Redirect, Switch, Link} from 'react-router-dom';
import SetupCheck from "./SetupCheck";
import TodayRates from "./TodayRates";
import HistoryRates from "./HistoryRates";

class Home extends Component {

    render() {
        return (
            <div>
                <nav className="navbar navbar-expand-lg navbar-dark bg-dark">
                    <Link className={"navbar-brand"} to={"#"}> Telemedi Zadanko </Link>
                    <div id="navbarText">
                        <ul className="navbar-nav mr-auto">
                            <li className="nav-item">
                                <Link className={"nav-link"} to={"/setup-check"}> React Setup Check </Link>
                            </li>

                        </ul>
                    </div>
                </nav>
                <Switch>
                    <Redirect exact from="/" to="/setup-check" />
                    <Route path="/setup-check" component={SetupCheck} />
                    <Route path="/rates" exact component={TodayRates} />
                    <Route path="/history/:currency" component={HistoryRates} />
                </Switch>

            </div>
        )
    }
}

export default Home;
