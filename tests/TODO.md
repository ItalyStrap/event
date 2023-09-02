# Da aggiungere al readme quando pronto

## Alcune basi del sistema di eventi (hooks) di WordPress

`do_action` è essenzialmente un wrapper per `apply_filters` con l'aggiunta di logica per gestire alcune variabili globali.

Aggiungere o rimuovere listener è possibile tramite `add_filter` e `remove_filter` che sono wrapper per `add_action` e `remove_action`, queste non aggiungono nulla e possono essere interscambiabili.
Il provider infatti utilizza `add_filter` e `remove_filter` per aggiungere e rimuovere i listener, i listener verranno poi aggiunti a `$wp_filter` che è la variabile globale usata per collezionare i listener.

## Introduzione

Alcune considerazioni prima d'iniziare, lo standard PSR-14 non è molto complesso, anzi, possiamo dire che sia piuttosto facile come concetto, anche il sistema utilizzato da WordPress alla fine non è molto complesso ma l'integrazione è stata comunque non semplice al primo tentativo.
Le API di WordPress in pratica sono dei wrapper per le, purtroppo, variabili globali che si occupano di gestire gli eventi e i listener, dopo una serie di tentativi senza riuscire a integrare le API nel modo giusto ho deciso di andare per la via dell'hardcoding implementando direttamente le globali usate nelle API di WordPress.
So che teoricamente sarebbe stato meglio l'utilizzo delle API perché la logica wrappata potrebbe cambiare un giorno ma, anche no, conoscendo WordPress questo non succederà mai, lol, quindi ho deciso di andare per la via più semplice e veloce.

Il Dispatcher è il componente che si occupa di gestire gli eventi e di notificare i listener registrati a essi.
Per iniziare la migrazione allo standard PSR-14, è stato creato un nuovo dispatcher che implementa l'interfaccia Psr\EventDispatcher\EventDispatcherInterface.
Il dispatcher precedente (EventDispatcher) è stato deprecato e verrà rimosso in una versione futura di questo pacchetto.

Il nuovo dispatcher è stato creato con lo scopo di eseguire solo il dispatch degli eventi lasciando al provider la gestione della registrazione dei listener.
Per questo motivo, il nuovo dispatcher non implementa l'interfaccia Psr\EventDispatcher\ListenerProviderInterface ma è possibile iniettare una istanza di Psr\EventDispatcher\ListenerProviderInterface nel dispatcher tramite il costruttore.
Tutto questo è stato fatto per separare la gestione degli eventi dalla gestione dei listener.

Il dispatcher utilizza la logica presente nel core di WordPress per la gestione degli eventi e dei listener, in pratica usa le stesse globali usate da `do_action` per popolare `$wp_current_filter` e `$wp_filters` e `$wp_actions` per consentire l'utilizzo delle funzioni `current_filter()`, `has_filter()` e `did_action()`.

Questo pacchetto infatti è pensato per poter integrare lo standard PSR-14 in WordPress.

Il provider è un aggregatore di listener e deve essere integrato una volta istanziato nel dispatcher per poter essere utilizzato.
Il provider usa la logica presente nel core di WordPress per la gestione dei listener, quindi l'aggiunta e la rimozione dei listener è uguale alle funzioni di WordPress `add_filter()` e `remove_filter()` l'unica eccezione è che i metodi per aggiungere e rimuovere i listenere non hanno il numero di argomenti come parametro visto che lo standard PSR-14 non prevede la possibilità di passare argomenti ai listener dato che esiste un unico argomento, l'Evento.
Per poter poi creare un iterator da passare al dispatcher, il provider utilizza la variabile globale `$wp_filter` utilizzata dal core per collezionare i vari listener.

Essendo compatibile con lo standard PSR-14, è possibile ovviamente utilizzare librerie terze che implementano lo standard PSR-14, piccola nota ovviamente questa è l'unica libreria che consente l'utilizzo anche delle API di WordPress per la gestione degli eventi.

Il provider si chiama `OrderedListenerProvider` perché di default le API di WordPress ritornano una lista preordinata di listener in base alla priorità, che è comunque possibile indicare nei metodi `addListener()` e `removeListener()` allo stesso modo in cui viene fatto con le funzioni di WordPress `add_filter()` e `remove_filter()`.

## Migrazione


