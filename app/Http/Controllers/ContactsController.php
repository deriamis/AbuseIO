<?php namespace AbuseIO\Http\Controllers;

use AbuseIO\Http\Requests;
//use AbuseIO\Http\Controllers\Controller;
use AbuseIO\Models\Contact;
use AbuseIO\Models\Netblock;
use AbuseIO\Models\Domain;
use Input;
use Redirect;
//use Illuminate\Http\Request;

class ContactsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {

        $contacts = Contact::paginate(10);

        return view('contacts.index')
            ->with('contacts', $contacts);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {

        return view('contacts.create');

    }

    /**
     * Export listing to CSV format.
     *
     * @return Response
     */
    public function export()
    {

        $contacts  = Contact::all();

        $columns    =
            [
                'reference'     => 'Reference',
                'contact'       => 'name',
                'enabled'       => 'Status',
                'email'         => 'E-Mail address',
                'rpc_host'      => 'RPC address',
                'rpc_key'       => 'RPC key',
                'auto_notify'   => 'Notifications',
            ];

        $output     = '"' . implode('", "', $columns) . '"' . PHP_EOL;

        foreach ($contacts as $contact) {

            $row =
                [
                    $contact->reference,
                    $contact->name,
                    $contact['enabled'] ? 'Enabled' : 'Disabled',
                    $contact['email'],
                    $contact['rpc_host'],
                    $contact['rpc_key'],
                    $contact['auto_notify'] ? 'Automatic' : 'Manual',
                ];

            $output .= '"' . implode('", "', $row) . '"' . PHP_EOL;

        }

        return response(substr($output, 0, -1), 200)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="Contacts.csv"');

    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {

        $input = Input::all();

        Contact::create($input);

        return Redirect::route('admin.contacts.index')
            ->with('message', 'Contact has been created');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show(Contact $contact)
    {

        $netblocks = Netblock::where('contact_id', '=', $contact->id)
            ->get();

        $domains   = Domain::where('contact_id', '=', $contact->id)
            ->get();

        return view('contacts.show')
            ->with('contact', $contact)
            ->with('netblocks', $netblocks)
            ->with('domains', $domains);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit(Contact $contact)
    {

        return view('contacts.edit')
            ->with('contact', $contact);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(Contact $contact)
    {

        $input = array_except(Input::all(), '_method');

        $contact->update($input);

        return Redirect::route('admin.contacts.show', $contact->id)
            ->with('message', 'Contact has been updated.');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy(Contact $contact)
    {

        $contact->delete();

        return Redirect::route('admin.contacts.index')
            ->with('message', 'Contact has been deleted.');

    }

}
